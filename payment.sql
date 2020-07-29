/*
SQLyog Community v13.1.1 (64 bit)
MySQL - 10.1.37-MariaDB : Database - new_ticket
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`new_ticket` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;

USE `new_ticket`;

/*Table structure for table `payment_gateways` */

DROP TABLE IF EXISTS `payment_gateways`;

CREATE TABLE `payment_gateways` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `provider_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `provider_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_on_site` tinyint(1) NOT NULL,
  `can_refund` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `admin_blade_template` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `checkout_blade_template` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `payment_gateways` */

insert  into `payment_gateways`(`id`,`provider_name`,`provider_url`,`is_on_site`,`can_refund`,`name`,`default`,`admin_blade_template`,`checkout_blade_template`) values 
(1,'Dummy/Test Gateway','none',1,1,'Dummy',0,'','Public.ViewEvent.Partials.Dummy'),
(2,'Stripe SCA','https://www.stripe.com',0,0,'Stripe\\PaymentIntents',0,'ManageAccount.Partials.StripeSCA','Public.ViewEvent.Partials.PaymentStripeSCA'),
(3,'Stripe','https://www.stripe.com',1,1,'Stripe',0,'ManageAccount.Partials.Stripe','Public.ViewEvent.Partials.PaymentStripe'),
(4,'Braintree','',1,1,'Braintree',1,'ManageAccount.Partials.Braintree','Public.ViewEvent.Partials.PaymentBraintree'),
(5,'Paypal','',1,1,'Paypal',0,'ManageAccount.Partials.Paypal','Public.ViewEvent.Partials.PaymentPaypal'),
(6,'Cash On Delivery','',1,1,'Cash On Delivery',0,'ManageAccount.Partials.CashOnDelivery','Public.ViewEvent.Partials.PaymentCashOnDelivery'),
(7,'Instamojo','',1,0,'Instamojo',0,'ManageAccount.Partials.Instamojo','Public.ViewEvent.Partials.PaymentInstamojo'),
(8,'Hyperpay','',1,0,'Hyperpay',0,'ManageAccount.Partials.Hyperpay','Public.ViewEvent.Partials.PaymentHyperpay'),
(9,'Razor Pay','',1,0,'Razor Pay',0,'ManageAccount.Partials.RazorPay','Public.ViewEvent.Partials.PaymentRazorPay');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
