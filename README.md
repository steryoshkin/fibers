# fibers

Итак....

Ubuntu Server

отсюда http://www.ubuntu.com/download/server качаем 14.04.3 LTS 64 битный

ставим

    apt-get install openssh-server
    apt-get install apache2 php5 curl php5-curl git
    php5enmod curl
    wget -qO- https://apt.boundlessgeo.com/gpg.key | apt-key add -
    echo "deb https://apt.boundlessgeo.com/suite/latest/ubuntu/ trusty main" > /etc/apt/sources.list.d/opengeo.list
    apt-get update
    apt-get install opengeo-server
    apt-get install php5-pgsql
    mv /var/www/html /var/www/html_old
    git clone https://github.com/steryoshkin/fibers /var/www/html
    cd /var/www/html
    chown -R www-data. *

добавить в /etc/postgresql/9.3/main/pg_hba.conf

    host    all             postgres        127.0.0.1/32            trust
    host    all             opengeo         127.0.0.1/32            trust
    host    all             all             внешний_ip/32         password

редактировать /etc/postgresql/9.3/main/postgresql.conf

    listen_addresses = '*'

редактировать /etc/php5/apache2/php.ini, найти переменную и изменить на On

    short_open_tag = On

После чего необходимо перезапустить apache

    service apache2 restart

Создаём базу

    sudo -u postgres psql < sql/create_user.sql
    sudo -u postgres psql < sql/create_fib.sql
    sudo -u postgres psql -d fib -c "CREATE EXTENSION postgis;"
    sudo -u postgres psql -d fib < sql/fib/create_fibers.sql
    
Создаём таблицы, админские логин/пасс в create_users.sql
    
    cat sql/fib/fibers/* | sudo -u postgres psql -d fib

редактировать fibers/engine/setup.php

    $host='172.17.2.249';
    $addr_obl='Кемеровская область';

где $host - ip адрес или хост веб сервера, $addr_obl - область, город, которые подставляются в nominatim, можно проверить https://nominatim.openstreetmap.org/

редактировать fibers/js/action.js

    var host = '172.17.2.249';

где host аналогично $host

редактировать fibers/geomap.php

    $lon=(@$_GET['lon']?$_GET['lon']:'73.43708');
    $lat=(@$_GET['lat']?$_GET['lat']:'61.257358');

где $lon - долгота, $lat - широта, править цифры 73.43708 и 61.257358

по дефолту логин пасс в опенгео admin/geoserver

заходим http://ip_адрес_либо_хост:8080/geoserver/web/

Добавляем стили

Заходим Данные -> Стили -> Добавить новый стиль

        Название:   cable
        Файл стиля: cable.sld

Жмём Отправить

Сделать аналогично стили cable_reserve и node

Файлы стилей находятся в папке sld.

Добавляем хранилище

Заходим Данные -> Хранилища -> Добавить новое хранилище -> Векторные источники данных -> PostGis

        Наименование источника данных: postgis
        database: fib
        schema: fibers
        user: st
        passwd: pass

Жмём Сохранить

Добавляем слои

Заходим Данные -> Слои -> Добавить новый ресурс -> opengeo:postgis

Выбираем слой cable и жмём Опубликовать

Вкладка Данные:

        Назварие: cable
        Охваты -> Вычислить из родного охвата

Должно получиться Родной охват: -1 -1 0 0, Широта/долгота ограничивающего поля: -1 -1 0 0

пс: Фигня не работает, будет казать только на карте, в редакторе не будет. Надо определать минимальные координаты, т.е. верх лево и максимальные, т.е. низ право и прописывать их. Я делаю проще, создаю один узел, добавляю в программе его и после уже редактирую слой, жму вычислить по данным, далее от минимальных градус отнимую, к максимальным прибавляю. Аналогичные координаты заношу во все слои и группу слоёв.

Вкладка Публикация:

        Параметры WMS -> Стиль по умолчанию -> cable

Жмём Сохранить

Сделать аналогично стили cable_reserve и node

Добавляем группу слоёв

Заходим Данные -> Группы слоёв -> Добавить новую группу слоев

        Назварие: map
        Слои -> Добавить слой... -> opengeo:cable
        Слои -> Добавить слой... -> opengeo:cable_reserve
        Слои -> Добавить слой... -> opengeo:node
        
        Границы -> Контрольная система координат -> EPSG:4326 -> Генерировать охват

Жмём Сохранить

Для возможности редактировать карту делаем полный доступ по wfs

Заходим Сервисы -> WFS -> Уровень обслуживания -> Полный (с блокировками)

Жмём Отправить

----------

Заходим в Редактор карты на главной странице программы, если всё сделали правильно - всё показывает.

Изначально центр непойми где, его можно изменить в файле /usr/share/opengeo/geoexplorer/WEB-INF/app/templates/composer.html -> center: [0, 0] и zoom: 2

Координаты либо пересчитать в метры, либо ручками вбивать и проверять на GeoExplorer-e

логинпасс как в геосервер

        выделяем слой node, редактировать -> создать
        выбираем точку -> сохранить

после чего можно добавлять в программе

Удалать в редакторе карты ничего нельзя, можно только добавлять узлы и редактировать кабель

перед работой необходимо заполнить справочники адресов

Видео по проекту https://youtu.be/-9gCLdVFJBY

skype: sergey_teryoshkin

Поддержка проекта: https://money.yandex.ru/to/410011465260309
