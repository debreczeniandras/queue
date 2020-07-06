# Bootstrap application for Message Queue Task

    cp .env.dist .env 

fill out the ports for redis Explorer GUI and web port.

    docker-compose up --build -d
    
api doc : 
    
    localhost:{yourport}/api/doc
    
api base path:

    localhost:{yourport}/api/v1/


## Info

The api has the following workflow

    Controller <--> Manager <--> Storage

This allows simple exchange of the storage layer and better testing. 


# Database Tasks 

Suggested entities for the shop are here:

    src/Entity/Shop/

## 2.1. Suggested DDL for shop system 

    -- article definition
    
    CREATE TABLE `article` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `price` int(11) NOT NULL,
      `inStock` tinyint(1) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    
    
    -- customer definition
    
    CREATE TABLE `customer` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    
    
    -- `order` definition
    
    CREATE TABLE `order` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `created` datetime NOT NULL,
      `total` int(11) NOT NULL,
      `discount` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    
    
    -- order_customer definition
    
    CREATE TABLE `order_customer` (
      `order_id` int(11) NOT NULL,
      `customer_id` int(11) NOT NULL,
      PRIMARY KEY (`order_id`,`customer_id`),
      KEY `IDX_60C16CB88D9F6D38` (`order_id`),
      KEY `IDX_60C16CB89395C3F3` (`customer_id`),
      CONSTRAINT `FK_60C16CB89395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE,
      CONSTRAINT `FK_60C16CB88D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    
    
    -- order_item definition
    
    CREATE TABLE `order_item` (
      `order_id` int(11) NOT NULL,
      `article_id` int(11) NOT NULL,
      `count` int(11) NOT NULL,
      `subtotal` int(11) NOT NULL,
      `discount` int(11) NOT NULL,
      PRIMARY KEY (`order_id`,`article_id`),
      KEY `IDX_52EA1F098D9F6D38` (`order_id`),
      KEY `IDX_52EA1F097294869C` (`article_id`),
      CONSTRAINT `FK_52EA1F097294869C` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
      CONSTRAINT `FK_52EA1F098D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    
## 2.2. - The Most expensive article:
    
    select * from article order by price desc limit 1
    
## 2.3 - The first 100 customers ordering laptops a since 1st Jan, 2012

    select c.* from customer c 
    	join order_customer oc on c.id = oc.customer_id 
    	join `order` o on o.id = oc.order_id 
    	join order_item  oi on oi.order_id = o.id
    	join article a on oi.article_id = a.id
    where a.name = 'laptop' 
    and o.created > '2012-01-01'
    limit 100 
    
## 2.4 Todo

Most spending customer per month.

WIP.

    SELECT
    	count() id,
    	DATE_FORMAT( o.created, '%M, %Y') spending_month
    from
    	customer c
    join order_customer oc on
    	c.id = oc.customer_id
    join `order` o on
    	o.id = oc.order_id
    join order_item oi on
    	oi.order_id = o.id
    join article a on
    	oi.article_id = a.id
    GROUP BY
    	spending_month
    
    