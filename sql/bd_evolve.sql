-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-08-2025 a las 19:10:18
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_evolve`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_log`
--

CREATE TABLE `audit_log` (
  `audit_id` bigint(20) NOT NULL COMMENT 'ID único del registro de auditoría',
  `table_name` varchar(100) NOT NULL COMMENT 'Nombre de la tabla afectada',
  `record_id` varchar(100) NOT NULL COMMENT 'ID del registro afectado',
  `action_type` enum('UPDATE','DELETE_LOGICAL','DELETE_PHYSICAL','INSERT') NOT NULL COMMENT 'Tipo de acción realizada sobre el registro',
  `action_by` char(36) NOT NULL COMMENT 'UUID del usuario que realizó la acción',
  `full_name` varchar(255) DEFAULT NULL COMMENT 'Nombre completo del usuario que realizó la acción',
  `user_type` varchar(255) DEFAULT NULL COMMENT 'Tipo o rol del usuario que realizó la acción',
  `action_timestamp` datetime DEFAULT current_timestamp() COMMENT 'Fecha y hora en que se realizó la acción',
  `action_timezone` varchar(255) DEFAULT NULL COMMENT 'Zona horaria del usuario al realizar la acción',
  `changes` text DEFAULT NULL COMMENT 'Cambios realizados en formato JSON o similar',
  `full_row` longtext DEFAULT NULL COMMENT 'Contenido completo del registro afectado en formato JSON',
  `client_ip` varchar(45) DEFAULT NULL COMMENT 'Dirección IP del cliente',
  `client_hostname` varchar(100) DEFAULT NULL COMMENT 'Nombre del host del cliente',
  `user_agent` text DEFAULT NULL COMMENT 'Cadena del navegador usada por el cliente',
  `client_os` varchar(50) DEFAULT NULL COMMENT 'Sistema operativo del cliente',
  `client_browser` varchar(50) DEFAULT NULL COMMENT 'Navegador del cliente',
  `domain_name` varchar(100) DEFAULT NULL COMMENT 'Dominio desde donde se ejecutó la acción',
  `request_uri` varchar(200) DEFAULT NULL COMMENT 'URI del recurso solicitado',
  `server_hostname` varchar(100) DEFAULT NULL COMMENT 'Hostname del servidor donde ocurrió la acción',
  `client_country` varchar(255) NOT NULL COMMENT 'País detectado del cliente mediante geo-IP',
  `client_region` varchar(255) NOT NULL COMMENT 'Región detectada del cliente mediante geo-IP',
  `client_city` varchar(255) NOT NULL COMMENT 'Ciudad detectada del cliente mediante geo-IP',
  `client_zipcode` varchar(255) NOT NULL COMMENT 'Código postal detectado del cliente mediante geo-IP',
  `client_coordinates` varchar(255) NOT NULL COMMENT 'Coordenadas geográficas detectadas del cliente (lat, long)',
  `geo_ip_timestamp` datetime DEFAULT NULL COMMENT 'Fecha de obtención de datos geo-IP',
  `geo_ip_timezone` varchar(255) DEFAULT NULL COMMENT 'Zona horaria detectada del cliente mediante geo-IP'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabla de auditoría del sistema que registra acciones realizadas sobre los datos por los usuarios';

--
-- Volcado de datos para la tabla `audit_log`
--

INSERT INTO `audit_log` (`audit_id`, `table_name`, `record_id`, `action_type`, `action_by`, `full_name`, `user_type`, `action_timestamp`, `action_timezone`, `changes`, `full_row`, `client_ip`, `client_hostname`, `user_agent`, `client_os`, `client_browser`, `domain_name`, `request_uri`, `server_hostname`, `client_country`, `client_region`, `client_city`, `client_zipcode`, `client_coordinates`, `geo_ip_timestamp`, `geo_ip_timezone`) VALUES
(1, 'categorias_proveedores', '7891d870-724a-11f0-bcf2-d05099fd1d9b', 'INSERT', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-05 18:21:02', 'America/Caracas', NULL, '{\"categoria_proveedor_id\": \"7891d870-724a-11f0-bcf2-d05099fd1d9b\", \"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"categoria_proveedor_nombre\": \"dffdfdfdfd\", \"categoria_proveedor_descripcion\": \"dfdfddfdfdfdf\", \"categoria_proveedor_tipo\": \"Material\", \"created_at\": \"2025-08-05 18:21:02\", \"created_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"updated_at\": \"2025-08-05 18:21:02\", \"updated_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"deleted_at\": null, \"deleted_by\": null}', '172.116.235.110', 'syn-172-116-235-110.res.spectrum.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', 'Windows 10', 'Mozilla Firefox', 'abse.siti.tech', '/sistema/modulos/categorias_proveedores/ajax/categoria_proveedor.ajax.php', '271F5AA', 'United States', 'California', 'Corona', '92879', '33.8789,-117.5353', '2025-08-05 15:21:02', 'America/Los_Angeles'),
(2, 'categorias_proveedores', '812783be-724a-11f0-bcf2-d05099fd1d9b', 'INSERT', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-05 18:21:16', 'America/Caracas', NULL, '{\"categoria_proveedor_id\": \"812783be-724a-11f0-bcf2-d05099fd1d9b\", \"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"categoria_proveedor_nombre\": \"xyz123abc\", \"categoria_proveedor_descripcion\": \"xyz123abc\", \"categoria_proveedor_tipo\": \"Material\", \"created_at\": \"2025-08-05 18:21:16\", \"created_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"updated_at\": \"2025-08-05 18:21:16\", \"updated_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"deleted_at\": null, \"deleted_by\": null}', '172.116.235.110', 'syn-172-116-235-110.res.spectrum.com', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', 'Windows 10', 'Mozilla Firefox', 'abse.siti.tech', '/sistema/modulos/categorias_proveedores/ajax/categoria_proveedor.ajax.php', '271F5AA', 'United States', 'California', 'Corona', '92879', '33.8789,-117.5353', '2025-08-05 15:21:16', 'America/Los_Angeles'),
(3, 'categorias_proveedores', 'dc4a247c-724a-11f0-bcf2-d05099fd1d9b', 'INSERT', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-05 18:23:49', 'America/Caracas', NULL, '{\"categoria_proveedor_id\": \"dc4a247c-724a-11f0-bcf2-d05099fd1d9b\", \"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"categoria_proveedor_nombre\": \"fsa\", \"categoria_proveedor_descripcion\": \"fsa\", \"categoria_proveedor_tipo\": \"Material\", \"created_at\": \"2025-08-05 18:23:49\", \"created_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"updated_at\": \"2025-08-05 18:23:49\", \"updated_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"deleted_at\": null, \"deleted_by\": null}', '200.8.108.199', '200.8.108.199', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/categorias_proveedores/ajax/categoria_proveedor.ajax.php', '271F5AA', 'Venezuela', 'Bolívar', 'Ciudad Bolívar', 'Unknown', '8.1187,-63.5517', '2025-08-05 18:23:49', 'America/Caracas'),
(4, 'categorias_gastos', 'b04f6d84-74ad-11f0-8010-d05099fd1d9b', 'INSERT', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:16:18', 'America/Caracas', NULL, '{\"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"nombre_categoria_gasto\": \"Accounting2\"}', '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/gastos/ajax/categorias_gastos.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:16:18', 'America/Los_Angeles'),
(5, 'categorias_gastos', 'b04f6d84-74ad-11f0-8010-d05099fd1d9b', 'DELETE_LOGICAL', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:16:23', 'America/Caracas', '{\"deleted_at\": {\"old\": null, \"new\": \"2025-08-08 19:16:23\"}}', '{\"categoria_gasto_id\": \"b04f6d84-74ad-11f0-8010-d05099fd1d9b\", \"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"nombre_categoria_gasto\": \"Accounting2\", \"created_at\": \"2025-08-08 19:16:18\", \"created_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"updated_at\": \"2025-08-08 19:16:18\", \"updated_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"deleted_at\": \"2025-08-08 19:16:23\", \"deleted_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\"}', '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/gastos/ajax/categorias_gastos.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:16:23', 'America/Los_Angeles'),
(6, 'categorias_proveedores', 'bdad8c0e-74ad-11f0-8010-d05099fd1d9b', 'INSERT', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:16:40', 'America/Caracas', NULL, '{\"categoria_proveedor_id\": \"bdad8c0e-74ad-11f0-8010-d05099fd1d9b\", \"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"categoria_proveedor_nombre\": \"fsafsaf2f2\", \"categoria_proveedor_descripcion\": \"fsafa\", \"categoria_proveedor_tipo\": \"Servicio\", \"created_at\": \"2025-08-08 19:16:40\", \"created_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"updated_at\": \"2025-08-08 19:16:40\", \"updated_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"deleted_at\": null, \"deleted_by\": null}', '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/categorias_proveedores/ajax/categoria_proveedor.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:16:40', 'America/Los_Angeles'),
(7, 'categorias_proveedores', '7891d870-724a-11f0-bcf2-d05099fd1d9b', 'DELETE_LOGICAL', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:16:50', 'America/Caracas', '{\"deleted_at\": {\"old\": null, \"new\": \"2025-08-08 19:16:50\"}}', '{\"categoria_proveedor_id\": \"7891d870-724a-11f0-bcf2-d05099fd1d9b\", \"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"categoria_proveedor_nombre\": \"dffdfdfdfd\", \"categoria_proveedor_descripcion\": \"dfdfddfdfdfdf\", \"categoria_proveedor_tipo\": \"Material\", \"created_at\": \"2025-08-05 18:21:02\", \"created_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"updated_at\": \"2025-08-08 19:16:50\", \"updated_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"deleted_at\": \"2025-08-08 19:16:50\", \"deleted_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\"}', '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/categorias_proveedores/ajax/categoria_proveedor.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:16:50', 'America/Los_Angeles'),
(8, 'categorias_proveedores', 'dc4a247c-724a-11f0-bcf2-d05099fd1d9b', 'DELETE_LOGICAL', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:16:54', 'America/Caracas', '{\"deleted_at\": {\"old\": null, \"new\": \"2025-08-08 19:16:54\"}}', '{\"categoria_proveedor_id\": \"dc4a247c-724a-11f0-bcf2-d05099fd1d9b\", \"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"categoria_proveedor_nombre\": \"fsa\", \"categoria_proveedor_descripcion\": \"fsa\", \"categoria_proveedor_tipo\": \"Material\", \"created_at\": \"2025-08-05 18:23:49\", \"created_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"updated_at\": \"2025-08-08 19:16:54\", \"updated_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"deleted_at\": \"2025-08-08 19:16:54\", \"deleted_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\"}', '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/categorias_proveedores/ajax/categoria_proveedor.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:16:54', 'America/Los_Angeles'),
(9, 'categorias_proveedores', '227edf24-6f2e-11f0-bcf2-d05099fd1d9b', 'DELETE_LOGICAL', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:16:58', 'America/Caracas', '{\"deleted_at\": {\"old\": null, \"new\": \"2025-08-08 19:16:58\"}}', '{\"categoria_proveedor_id\": \"227edf24-6f2e-11f0-bcf2-d05099fd1d9b\", \"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"categoria_proveedor_nombre\": \"fsafa\", \"categoria_proveedor_descripcion\": \"fsafa\", \"categoria_proveedor_tipo\": \"Material\", \"created_at\": \"2025-08-01 19:20:38\", \"created_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"updated_at\": \"2025-08-08 19:16:58\", \"updated_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"deleted_at\": \"2025-08-08 19:16:58\", \"deleted_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\"}', '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/categorias_proveedores/ajax/categoria_proveedor.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:16:58', 'America/Los_Angeles'),
(10, 'categorias_proveedores', 'bdad8c0e-74ad-11f0-8010-d05099fd1d9b', 'DELETE_LOGICAL', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:17:05', 'America/Caracas', '{\"deleted_at\": {\"old\": null, \"new\": \"2025-08-08 19:17:05\"}}', '{\"categoria_proveedor_id\": \"bdad8c0e-74ad-11f0-8010-d05099fd1d9b\", \"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"categoria_proveedor_nombre\": \"fsafsaf2f2\", \"categoria_proveedor_descripcion\": \"fsafa\", \"categoria_proveedor_tipo\": \"Servicio\", \"created_at\": \"2025-08-08 19:16:40\", \"created_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"updated_at\": \"2025-08-08 19:17:05\", \"updated_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"deleted_at\": \"2025-08-08 19:17:05\", \"deleted_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\"}', '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/categorias_proveedores/ajax/categoria_proveedor.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:17:05', 'America/Los_Angeles'),
(11, 'categorias_proveedores', '812783be-724a-11f0-bcf2-d05099fd1d9b', 'DELETE_LOGICAL', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:17:13', 'America/Caracas', '{\"deleted_at\": {\"old\": null, \"new\": \"2025-08-08 19:17:13\"}}', '{\"categoria_proveedor_id\": \"812783be-724a-11f0-bcf2-d05099fd1d9b\", \"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"categoria_proveedor_nombre\": \"xyz123abc\", \"categoria_proveedor_descripcion\": \"xyz123abc\", \"categoria_proveedor_tipo\": \"Material\", \"created_at\": \"2025-08-05 18:21:16\", \"created_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"updated_at\": \"2025-08-08 19:17:13\", \"updated_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\", \"deleted_at\": \"2025-08-08 19:17:13\", \"deleted_by\": \"4de32685-d034-4a1f-8e66-2e48535593a4\"}', '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/categorias_proveedores/ajax/categoria_proveedor.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:17:13', 'America/Los_Angeles'),
(12, 'codigos_descuento', 'bb803dde-9aba-48cf-9700-1d6c378e21d5', 'UPDATE', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:19:50', 'America/Caracas', '{\"descuento_valor\":{\"old\":\"10.00\",\"new\":\"11.00\"}}', NULL, '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/descuentos/ajax/codigos_descuento.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:19:50', 'America/Los_Angeles'),
(13, 'logos_empresa', 'logos_688285cdab1666.41492110', 'UPDATE', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:20:38', 'America/Caracas', '{\"logo_color\":{\"old\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/logo_color_688d5e8520bac.svg\",\"new\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/logo_color_689686465f94e.svg\"}}', NULL, '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/logosEmpresas/ajax/logosEmpresa.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:20:38', 'America/Los_Angeles'),
(14, 'logos_empresa', 'logos_688285cdab1666.41492110', 'UPDATE', '4de32685-d034-4a1f-8e66-2e48535593a4', 'Moises Celis', 'administrador', '2025-08-08 19:21:43', 'America/Caracas', '{\"favicon\":{\"old\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/favicon_688d5e7d9c7d2.svg\",\"new\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/favicon_68968687a0d3c.svg\"}}', NULL, '89.41.26.60', '89.41.26.60', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/logosEmpresas/ajax/logosEmpresa.ajax.php', '271F5AA', 'United States', 'California', 'Los Angeles', '90014', '34.0481,-118.2531', '2025-08-08 16:21:43', 'America/Los_Angeles'),
(15, 'logos_empresa', 'logos_688285cdab1666.41492110', 'UPDATE', 'acaf0d3e-6329-11f0-85dd-40c2ba2ee6c6', 'james soscue', 'administrador', '2025-08-15 18:21:22', 'America/Los_Angeles', '{\"logo_color\":{\"old\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/logo_color_689686465f94e.svg\",\"new\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/logo_color_689fdd1288c4b.svg\"}}', NULL, '186.82.168.208', 'dynamic-ip-18682168208.cable.net.co', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse.siti.tech', '/sistema/modulos/logosEmpresas/ajax/logosEmpresa.ajax.php', '271F5AA', 'Colombia', 'Cauca Department', 'Popayán', '190003', '2.4402,-76.6123', '2025-08-15 20:21:22', 'America/Bogota'),
(16, 'logos_empresa', 'logos_688285cdab1666.41492110', 'UPDATE', 'acaf0d3e-6329-11f0-85dd-40c2ba2ee6c6', 'james soscue', 'administrador', '2025-08-15 18:21:36', 'America/Los_Angeles', '{\"favicon\":{\"old\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/favicon_68968687a0d3c.svg\",\"new\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/favicon_689fdd2082a96.svg\"}}', NULL, '186.82.168.208', 'dynamic-ip-18682168208.cable.net.co', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse.siti.tech', '/sistema/modulos/logosEmpresas/ajax/logosEmpresa.ajax.php', '271F5AA', 'Colombia', 'Cauca Department', 'Popayán', '190003', '2.4402,-76.6123', '2025-08-15 20:21:36', 'America/Bogota'),
(17, 'clientes', 'a17c3392-7a47-11f0-bcfc-d05099fd1d9b', 'INSERT', 'acaf0d3e-6329-11f0-85dd-40c2ba2ee6c6', 'james soscue', 'administrador', '2025-08-15 19:20:51', 'America/Los_Angeles', NULL, '{\"empresa_id\": \"2683dd73-6339-11f0-85dd-40c2ba2ee6c6\", \"cliente_nombre\": \"james\", \"cliente_apellido\": \"soscue\", \"cliente_fecha_nacimiento\": \"2025-07-29\", \"cliente_familia\": \"james\", \"cliente_correo\": \"james@jms.com\", \"cliente_fax\": \"\", \"cliente_telefono\": \"(313) 555-0520\", \"cliente_ciudad\": \"popayan\", \"cliente_estado_us\": \"cauca\", \"cliente_codigo_postal\": \"324\"}', '186.82.168.208', 'dynamic-ip-18682168208.cable.net.co', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse.siti.tech', '/sistema/modulos/clientes/ajax/cliente.ajax.php', '271F5AA', 'Colombia', 'Cauca Department', 'Popayán', '190003', '2.4402,-76.6123', '2025-08-15 21:20:51', 'America/Bogota'),
(18, 'empresas', '2683dd73-6339-11f0-85dd-40c2ba2ee6c6', 'UPDATE', 'acaf0d3e-6329-11f0-85dd-40c2ba2ee6c6', 'james soscue', 'administrador', '2025-08-22 06:01:07', 'America/Los_Angeles', '{\"firma_contratista\":{\"old\":\"cargas/usuarios/firmas/firma_688d5ea009919_firma-digital.png\",\"new\":\"cargas/usuarios/firmas/firma_e8548f6b809f.png\"}}', NULL, '186.82.168.208', 'dynamic-ip-18682168208.cable.net.co', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/empresa/ajax/empresas.ajax.php', '271F5AA', 'Colombia', 'Cauca Department', 'Popayán', '190003', '2.4402,-76.6123', '2025-08-22 08:01:07', 'America/Bogota'),
(19, 'logos_empresa', 'logos_688285cdab1666.41492110', 'UPDATE', 'acaf0d3e-6329-11f0-85dd-40c2ba2ee6c6', 'james soscue', 'administrador', '2025-08-22 06:01:45', 'America/Los_Angeles', '{\"favicon\":{\"old\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/favicon_689fdd2082a96.svg\",\"new\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/favicon_68a86a3974850.svg\"}}', NULL, '186.82.168.208', 'dynamic-ip-18682168208.cable.net.co', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/logosEmpresas/ajax/logosEmpresa.ajax.php', '271F5AA', 'Colombia', 'Cauca Department', 'Popayán', '190003', '2.4402,-76.6123', '2025-08-22 08:01:45', 'America/Bogota'),
(20, 'logos_empresa', 'logos_688285cdab1666.41492110', 'UPDATE', 'acaf0d3e-6329-11f0-85dd-40c2ba2ee6c6', 'james soscue', 'administrador', '2025-08-22 06:02:05', 'America/Los_Angeles', '{\"logo_color\":{\"old\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/logo_color_689fdd1288c4b.svg\",\"new\":\"cargas/aplicacion/2683dd73-6339-11f0-85dd-40c2ba2ee6c6/logo_color_68a86a4d469a8.svg\"}}', NULL, '186.82.168.208', 'dynamic-ip-18682168208.cable.net.co', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'Windows 10', 'Google Chrome', 'abse-desarrollo.siti.tech', '/sistema/modulos/logosEmpresas/ajax/logosEmpresa.ajax.php', '271F5AA', 'Colombia', 'Cauca Department', 'Popayán', '190003', '2.4402,-76.6123', '2025-08-22 08:02:05', 'America/Bogota');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cities`
--

CREATE TABLE `cities` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `province_id` char(36) NOT NULL,
  `name` varchar(120) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cities`
--

INSERT INTO `cities` (`id`, `province_id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
('0ac618fe-810d-11f0-bd4d-41b8da1d00a8', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', 'Fort St. John', 1, '2025-08-24 13:09:06', '2025-08-24 13:09:06'),
('0ac62a97-810d-11f0-bd4d-41b8da1d00a8', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', 'Taylor', 1, '2025-08-24 13:09:06', '2025-08-24 13:09:06'),
('0ac62b6b-810d-11f0-bd4d-41b8da1d00a8', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', 'Charlie Lake', 1, '2025-08-24 13:09:06', '2025-08-24 13:09:06'),
('0ac62bda-810d-11f0-bd4d-41b8da1d00a8', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', 'Dawson Creek', 1, '2025-08-24 13:09:06', '2025-08-24 13:09:06'),
('0ac62c43-810d-11f0-bd4d-41b8da1d00a8', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', 'Chetwynd', 1, '2025-08-24 13:09:06', '2025-08-24 13:09:06'),
('0ac62d37-810d-11f0-bd4d-41b8da1d00a8', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', 'Hudson\'s Hope', 1, '2025-08-24 13:09:06', '2025-08-24 13:09:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_emails`
--

CREATE TABLE `contact_emails` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `entity_type` enum('customer','worker') NOT NULL,
  `entity_id` char(36) NOT NULL,
  `email` varchar(190) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_phones`
--

CREATE TABLE `contact_phones` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `entity_type` enum('customer','worker') NOT NULL,
  `entity_id` char(36) NOT NULL,
  `phone_type` enum('mobile','office','fax','other') NOT NULL DEFAULT 'mobile',
  `country_code` varchar(8) DEFAULT NULL,
  `phone_number` varchar(50) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `countries`
--

CREATE TABLE `countries` (
  `country_id` char(36) NOT NULL,
  `suffix` varchar(5) DEFAULT NULL,
  `full_prefix` varchar(20) DEFAULT NULL,
  `normalized_prefix` varchar(10) DEFAULT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `phone_mask` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `countries`
--

INSERT INTO `countries` (`country_id`, `suffix`, `full_prefix`, `normalized_prefix`, `country_name`, `phone_mask`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('00515e61-97a8-425b-a2cb-421258dce0a4', 'NC', 'NC +687', '+687', 'New Caledonia', '+687 ##########', NULL, NULL, '2025-07-07 17:15:04', '3', NULL, NULL),
('00be12a2-a9bd-44af-873b-67b1eedf28ab', 'PY', 'PY +595', '+595', 'Paraguay', '+595 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('02241709-6796-4802-aa9c-3adfa967bcaa', 'VU', 'VU +678', '+678', 'Vanuatu', '+678 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('023da98e-c2b1-4063-848e-cc28b5bf7f74', 'UM', 'UM +268', '+268', 'United States Minor Outlying Islands', '+268 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('04ff54e0-d703-4406-8e78-eb3c9d65bebd', 'TD', 'TD +235', '+235', 'Chad', '+235 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('07361581-d8ae-4d78-baed-572b6226eff4', 'SV', 'SV +503', '+503', 'El Salvador', '+503 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0895d4fa-829f-43d2-88c0-437c1a7d913a', 'PL', 'PL +48', '+48', 'Poland', '+48 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('09f7908f-fb9f-4b33-8f89-198509125a3c', 'ST', 'ST +239', '+239', 'São Tomé and Príncipe', '+239 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0b0422b1-4682-4218-91ef-d634ce4294c1', 'BW', 'BW +267', '+267', 'Botswana', '+267 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0b774d90-da1b-44f6-95d2-df02446d990a', 'KG', 'KG +996', '+996', 'Kyrgyzstan', '+996 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0b806713-c15f-4518-a17d-33102aa2e8bd', 'KW', 'KW +965', '+965', 'Kuwait', '+965 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0c600409-392d-4905-a3f3-f9fe02f70e2c', 'MF', 'MF +590', '+590', 'Saint Martin', '+590 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0ce52136-2a65-46e0-9186-7f9a154b29b2', 'IO', 'IO +246', '+246', 'British Indian Ocean Territory', '+246 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('0f9afd8a-6b59-42c9-a507-a8a496e83c47', 'MS', 'MS +1664', '+1664', 'Montserrat', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('117aa9fc-0a95-4fe2-965f-bf34ff2b2bae', 'GS', 'GS +500', '+500', 'South Georgia', '+500 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('11ecc294-4943-4389-9c89-084974f840d7', 'SA', 'SA +966', '+966', 'Saudi Arabia', '+966 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('12a2946b-b242-43c1-90c2-93c0fd5d45bc', 'ZM', 'ZM +260', '+260', 'Zambia', '+260 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('13f29df4-c3d8-4646-a598-d076aa5db905', 'EH', 'EH +2125288', '+2125288', 'Western Sahara', '+2125288 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('150d409f-9dfa-4cb4-9ebd-a500b71e3a37', 'KZ', 'KZ +76', '+76', 'Kazakhstan', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('15a9cb5a-d684-403d-b2a9-cc79187ba4a3', 'GN', 'GN +224', '+224', 'Guinea', '+224 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('172f7a9a-b2d6-4157-bf76-4150f4eab724', 'AT', 'AT +43', '+43', 'Austria', '+43 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('17cd7262-0c6c-48b5-b910-dc1cac8fc3cd', 'SC', 'SC +248', '+248', 'Seychelles', '+248 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('181ae6c0-e13e-4a4a-a6bb-b257a81e96d4', 'BE', 'BE +32', '+32', 'Belgium', '+32 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('186e03f7-8862-4244-ba98-636141d94535', 'VN', 'VN +84', '+84', 'Vietnam', '+84 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('18c21a6a-72bc-4bc2-bfbe-6fbd5c8481d8', 'PR', 'PR +1939', '+1939', 'Puerto Rico', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('18c2d597-2d4f-4979-b6b2-5e28ff1b7ced', 'BV', 'BV +47', '+47', 'Bouvet Island', '+47 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('19a78efc-f89c-4fc0-b209-a49eddeef4f2', 'MT', 'MT +356', '+356', 'Malta', '+356 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('1a209735-8a64-438c-bbf0-38184c587a26', 'SG', 'SG +65', '+65', 'Singapore', '+65 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('1a80be2c-bbc6-4b52-9c8e-15a18b06636f', 'GG', 'GG +44', '+44', 'Guernsey', '+44 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('1b8f7f56-3238-41cf-b2ea-d4bc03892006', 'PM', 'PM +508', '+508', 'Saint Pierre and Miquelon', '+508 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('1e0f74bd-0fc8-435a-838c-b326175d8abb', 'AM', 'AM +374', '+374', 'Armenia', '+374 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('1f8db347-bba7-4dcf-b8ea-4f2c6b630472', 'IR', 'IR +98', '+98', 'Iran', '+98 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('236adbcd-59b2-4e9b-b9e4-661119918ca9', 'PT', 'PT +351', '+351', 'Portugal', '+351 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('243d9bed-232d-42df-a467-9125a3438a09', 'TH', 'TH +66', '+66', 'Thailand', '+66 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('256ae45c-865a-4d63-81da-45e6e2aa7fa6', 'PG', 'PG +675', '+675', 'Papua New Guinea', '+675 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('27b00f30-ba4a-42cd-aa27-dca3a9ae9372', 'SS', 'SS +211', '+211', 'South Sudan', '+211 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2a6ceb75-6bf0-4548-842a-98879a7b3ae2', 'RS', 'RS +381', '+381', 'Serbia', '+381 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2aafd6d1-071c-4863-bd84-15c92f475c8d', 'SH', 'SH +247', '+247', 'Saint Helena, Ascension and Tristan da Cunha', '+247 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2bec19d3-8f4e-45f0-9bad-212301747e28', 'CX', 'CX +61', '+61', 'Christmas Island', '+61 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2dc825b1-105d-4806-8237-fc583d829fb9', 'RU', 'RU +74', '+74', 'Russia', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('2df76923-00b8-436f-9bf2-464c26d8a646', 'SI', 'SI +386', '+386', 'Slovenia', '+386 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2ec8166a-f385-4f03-b43f-e1587c9cfe9e', 'SZ', 'SZ +268', '+268', 'Eswatini', '+268 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('2feb67cc-1b12-4ae1-be72-db7a17523810', 'DO', 'DO +1809', '+1809', 'Dominican Republic', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('30424f52-4928-407e-bb57-5bc81d5c20aa', 'JO', 'JO +962', '+962', 'Jordan', '+962 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('30816865-63e7-4e0b-8c41-ca2058c4bd81', 'JM', 'JM +1876', '+1876', 'Jamaica', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('30adab4a-d730-43bc-a367-9c2b70016f35', 'RE', 'RE +262', '+262', 'Réunion', '+262 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('32fa3d92-2874-4d38-8192-3a425b66263f', 'TW', 'TW +886', '+886', 'Taiwan', '+886 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('337b9f29-1782-464a-a20c-3f1265b0f886', 'WF', 'WF +681', '+681', 'Wallis and Futuna', '+681 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('33911e4d-e11c-4289-885c-f3cd16dc050e', 'AS', 'AS +1684', '+1684', 'American Samoa', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('33c2dacf-fd24-4ef5-9c62-233672605dcb', 'NR', 'NR +674', '+674', 'Nauru', '+674 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('33e9a261-9b23-4c6a-804b-693c53d91a90', 'BM', 'BM +1441', '+1441', 'Bermuda', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('346ce5e4-b1dd-4a21-a462-a8c5b67c864c', 'TR', 'TR +90', '+90', 'Turkey', '+90 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3555485b-dcf9-4fcb-8b71-bca3fede6ad7', 'UG', 'UG +256', '+256', 'Uganda', '+256 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('360db8df-6095-41a0-9ad2-8cc519a58f65', 'ID', 'ID +62', '+62', 'Indonesia', '+62 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('36286fb2-83c2-4d06-bece-27c658dd837f', 'PH', 'PH +63', '+63', 'Philippines', '+63 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('36c8de7e-d5c1-4dff-83f4-d47adbd8f0bd', 'HK', 'HK +852', '+852', 'Hong Kong', '+852 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('36ec1e0d-d549-4e4f-984d-843f990be788', 'CG', 'CG +242', '+242', 'Republic of the Congo', '+242 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('37459f20-57d9-41f1-a09f-c7fbead93577', 'HR', 'HR +385', '+385', 'Croatia', '+385 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('37e2b227-95b2-45ff-b578-c7c7558bc6de', 'PS', 'PS +970', '+970', 'Palestine', '+970 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3843aba3-bcae-49f2-91b6-3c187c414a1f', 'GW', 'GW +245', '+245', 'Guinea-Bissau', '+245 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3a16fd94-0ed2-49f5-9913-b95b889853a8', 'HT', 'HT +509', '+509', 'Haiti', '+509 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3a31848b-438f-4b21-bdfe-41e33d9a7314', 'SD', 'SD +249', '+249', 'Sudan', '+249 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3a630ca2-5bbb-4d77-8a5f-1cc624be6107', 'MU', 'MU +230', '+230', 'Mauritius', '+230 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3ac7a7f6-a4a7-4e4a-93f1-868e68074e00', 'TG', 'TG +228', '+228', 'Togo', '+228 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3b7b8004-2944-4345-b571-a748e68df5fc', 'VG', 'VG +1284', '+1284', 'British Virgin Islands', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('3cd8564a-6bcd-4206-86e3-42cd594d81b7', 'IQ', 'IQ +964', '+964', 'Iraq', '+964 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3e52e28c-e5ea-4aa4-8845-9e4522f036f1', 'DJ', 'DJ +253', '+253', 'Djibouti', '+253 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3f0a7573-1756-4a5f-8079-5f800358b6d1', 'AZ', 'AZ +994', '+994', 'Azerbaijan', '+994 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('3feb9545-1778-415b-9dfe-b5fd9ca08338', 'LV', 'LV +371', '+371', 'Latvia', '+371 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('40dabfb4-0947-488c-924f-4f67bfaa6ade', 'IT', 'IT +39', '+39', 'Italy', '+39 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('41e2a4da-7343-4fa2-a8af-442c9895e92d', 'MV', 'MV +960', '+960', 'Maldives', '+960 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('4258c16f-ea69-4832-8070-3e0087670aad', 'FM', 'FM +691', '+691', 'Micronesia', '+691 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('430f1b42-b56b-40b9-b5e8-33b0c96d228f', 'AI', 'AI +1264', '+1264', 'Anguilla', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('452777a5-f606-454c-8863-82dc16c9a3b5', 'PA', 'PA +507', '+507', 'Panama', '+507 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('45c6e8ac-ac5d-4e56-a959-35085844c7d6', 'AX', 'AX +35818', '+35818', 'Åland Islands', '+35818 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('4666683d-8062-44ce-b1ae-2458cd31f8de', 'TC', 'TC +1649', '+1649', 'Turks and Caicos Islands', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('4717f9bc-16f7-4905-b77f-b9089eb24eeb', 'JP', 'JP +81', '+81', 'Japan', '+81 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('49df48c1-bc7c-47b9-ae2c-6530d6ab7de6', 'GB', 'GB +44', '+44', 'United Kingdom', '+44 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('49ed8c8d-f7cd-4bb0-801e-380310652663', 'RU', 'RU +78', '+78', 'Russia', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('4bec0e66-c030-46cb-880b-5b5a030e9721', 'MG', 'MG +261', '+261', 'Madagascar', '+261 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('4d1b7282-ea47-44fe-9287-be4b6f9388d6', 'TJ', 'TJ +992', '+992', 'Tajikistan', '+992 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('4e4ebd49-0d86-4a09-8469-1e3dd4e7f916', 'NG', 'NG +234', '+234', 'Nigeria', '+234 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('4fbe5e27-4ae8-4d14-be25-5dc17eb5bede', 'SX', 'SX +1721', '+1721', 'Sint Maarten', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('50f5e741-f4c2-4812-8c7c-ce0fc1603696', 'RU', 'RU +79', '+79', 'Russia', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('51ac912b-6d39-4614-ae9d-59c2dd902738', 'BT', 'BT +975', '+975', 'Bhutan', '+975 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('528259cf-102c-4b0c-a02c-99a6f8ddd3a0', 'GF', 'GF +594', '+594', 'French Guiana', '+594 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('538c55c0-a7e4-4fd1-b1be-ffefd136a0a2', 'SH', 'SH +290', '+290', 'Saint Helena, Ascension and Tristan da Cunha', '+290 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('555a2c1c-9740-46ad-ad0b-0569e0145c83', 'KE', 'KE +254', '+254', 'Kenya', '+254 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('555b7767-01f8-4968-957c-baf7cb41b859', 'TK', 'TK +690', '+690', 'Tokelau', '+690 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5575d6c9-0aa7-448c-a4b7-7bf3df94f1b2', 'FI', 'FI +358', '+358', 'Finland', '+358 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5669ad4c-5e5e-4357-9629-bac4fcdea95b', 'GE', 'GE +995', '+995', 'Georgia', '+995 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('56d6ad24-5d5b-4b5a-9717-2b49b161394c', 'RO', 'RO +40', '+40', 'Romania', '+40 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('583da943-9671-4de9-a492-dc04d15fef1f', 'CU', 'CU +53', '+53', 'Cuba', '+53 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5a815258-b828-4b67-a0ee-222c168454d9', 'BJ', 'BJ +229', '+229', 'Benin', '+229 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5ab652a7-a5e3-4561-9fb8-cc29e796cb2a', 'DM', 'DM +1767', '+1767', 'Dominica', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('5af36b29-0a70-44fb-b83f-afacf79386f3', 'MM', 'MM +95', '+95', 'Myanmar', '+95 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5b33f793-e4dc-41fc-b1e1-e9a478b8df20', 'IN', 'IN +91', '+91', 'India', '+91 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5baa0939-8041-4347-8918-b10dc372c0e2', 'GP', 'GP +590', '+590', 'Guadeloupe', '+590 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5f4b1996-898b-41de-8967-fa25073f9022', 'CZ', 'CZ +420', '+420', 'Czechia', '+420 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5f53943d-ea4f-42dd-8813-9c36b3e4c6a7', 'NP', 'NP +977', '+977', 'Nepal', '+977 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('5f7d6817-4868-47e1-8308-85e04e97d351', 'MC', 'MC +377', '+377', 'Monaco', '+377 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('60efcdc8-ad9f-40cc-b87a-2c48a9b76b76', 'ZW', 'ZW +263', '+263', 'Zimbabwe', '+263 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('6226422a-4a3c-4f20-b88c-99d55adecd38', 'DO', 'DO +1849', '+1849', 'Dominican Republic', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('63005142-7990-49a5-ac06-78bc0a24fae5', 'GD', 'GD +1473', '+1473', 'Grenada', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('65e4f419-d731-4672-a786-f3613a274276', 'FO', 'FO +298', '+298', 'Faroe Islands', '+298 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('66878b27-7801-41d4-bd13-1c228af0e33b', 'MQ', 'MQ +596', '+596', 'Martinique', '+596 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('68cfa791-524e-4a58-bf30-4aa170f6dfcb', 'GA', 'GA +241', '+241', 'Gabon', '+241 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('6ccd2437-2f75-4d59-b82f-06a2869f85ad', 'BL', 'BL +590', '+590', 'Saint Barthélemy', '+590 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('6f37c015-2878-405a-a63d-2e4e1bbe598e', 'IM', 'IM +44', '+44', 'Isle of Man', '+44 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('73478714-0d8b-4027-85af-4fa807977615', 'LR', 'LR +231', '+231', 'Liberia', '+231 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7383714f-6513-4481-8001-94834d590408', 'FK', 'FK +500', '+500', 'Falkland Islands', '+500 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('743e11dc-be0f-4299-9beb-6d4af51f7151', 'TF', 'TF +262', '+262', 'French Southern and Antarctic Lands', '+262 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('748a87e3-d032-493f-af6e-3f2ffe590fb7', 'AD', 'AD +376', '+376', 'Andorra', '+376 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('75132070-b850-4703-baeb-5d18bba8f4aa', 'SN', 'SN +221', '+221', 'Senegal', '+221 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7683ced1-4248-4249-b71c-adbb41abcfbf', 'CM', 'CM +237', '+237', 'Cameroon', '+237 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('778f23c0-a58d-4e23-b84d-4f87296089c8', 'KI', 'KI +686', '+686', 'Kiribati', '+686 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7834a4f5-7c89-48d9-b2a6-37d6a1980cfb', 'SK', 'SK +421', '+421', 'Slovakia', '+421 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('79662e12-2501-4479-9b92-6dd8dcf9ac7c', 'TM', 'TM +993', '+993', 'Turkmenistan', '+993 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7bf69417-d304-4ede-adab-651a2662e341', 'KN', 'KN +1869', '+1869', 'Saint Kitts and Nevis', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('7c6b9c41-b3c2-4865-9a18-a490dd1d4326', 'VI', 'VI +1340', '+1340', 'United States Virgin Islands', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('7cd107c1-0e39-4838-baca-4b815f047cb4', 'PR', 'PR +1787', '+1787', 'Puerto Rico', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('7cd3c3f0-1d89-4a0e-a197-8aba56caa24f', 'MW', 'MW +265', '+265', 'Malawi', '+265 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7dbe5abf-3888-4528-a470-e4b9212e0f9e', 'CC', 'CC +61', '+61', 'Cocos (Keeling) Islands', '+61 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('7decda8a-a95c-4f5e-8616-d01b29dc95ce', 'ES', 'ES +34', '+34', 'Spain', '+34 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('81b355e0-99e6-4552-8add-c16dcf94a20b', 'AO', 'AO +244', '+244', 'Angola', '+244 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('81e4c781-e590-44ae-a053-f96e360f6bf1', 'EG', 'EG +20', '+20', 'Egypt', '+20 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('820dcf7c-4c09-4f1d-b807-2ad94bbe66e2', 'TV', 'TV +688', '+688', 'Tuvalu', '+688 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('822852e5-49d8-4755-b18a-69b910bcc074', 'GH', 'GH +233', '+233', 'Ghana', '+233 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('824bbc9b-9341-4b78-860b-4bc996a828cd', 'XK', 'XK +383', '+383', 'Kosovo', '+383 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('85325c6d-2ccf-4702-9629-39a618b88515', 'SE', 'SE +46', '+46', 'Sweden', '+46 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('86c4e3fe-72d9-4680-8dd7-42743ba180b0', 'GL', 'GL +299', '+299', 'Greenland', '+299 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('889907ae-f300-48ad-9742-48c01cf56cbb', 'VA', 'VA +3906698', '+3906698', 'Vatican City', '+3906698 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('891b19ef-5fa2-41be-95f6-86199b1eb04e', 'UZ', 'UZ +998', '+998', 'Uzbekistan', '+998 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8a4d0d30-445d-47c6-8917-894ac26e5c11', 'KR', 'KR +82', '+82', 'South Korea', '+82 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8a9d73f9-b096-459c-82e6-48bc4deea454', 'MD', 'MD +373', '+373', 'Moldova', '+373 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8aa1aee0-8311-4f4b-aef5-0b1eb2eaca2c', 'EH', 'EH +2125289', '+2125289', 'Western Sahara', '+2125289 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8b3be802-68b6-4417-89df-209f9f2f434f', 'TO', 'TO +676', '+676', 'Tonga', '+676 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8c048c15-67f3-429c-8a08-130d7b831e22', 'SJ', 'SJ +4779', '+4779', 'Svalbard and Jan Mayen', '+4779 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8c9d3073-44bc-47dc-b43d-eb2162926ba4', 'VA', 'VA +379', '+379', 'Vatican City', '+379 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8d6fa47e-6ba7-49a5-8cd3-430b619ac80d', 'ER', 'ER +291', '+291', 'Eritrea', '+291 ##########', NULL, NULL, '2025-07-05 07:48:35', '1', NULL, NULL),
('8da21574-f216-49de-93f3-6870839a7535', 'PF', 'PF +689', '+689', 'French Polynesia', '+689 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('8fd023db-4874-415b-9471-2c502ef01218', 'AF', 'AF +93', '+93', 'Afghanistan', '+93 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('903935fd-369b-4cae-9ba3-7b774d77db9c', 'AU', 'AU +61', '+61', 'Australia', '+61 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('90d6be62-be87-4a2d-8e64-54f5b149a37e', 'LS', 'LS +266', '+266', 'Lesotho', '+266 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('91028643-d017-43b7-90fe-642ba8c16050', 'EE', 'EE +372', '+372', 'Estonia', '+372 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('91a8f0f4-45ec-49c5-a2ab-e4e3c0aa642f', 'BA', 'BA +387', '+387', 'Bosnia and Herzegovina', '+387 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('93f8eba0-fe82-40ee-9a3d-3eee046071fd', 'QA', 'QA +974', '+974', 'Qatar', '+974 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('94fb6f9d-337f-40e9-9d2e-614e8aeddb4a', 'FR', 'FR +33', '+33', 'France', '+33 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('96437b88-4199-4242-b6f1-6f2c3758b406', 'UY', 'UY +598', '+598', 'Uruguay', '+598 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('98244349-53b5-4cb6-bde8-0d7ca2f339d7', 'MN', 'MN +976', '+976', 'Mongolia', '+976 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('9eaf4a96-597c-4dda-ae2d-156b022e4a3c', 'CY', 'CY +357', '+357', 'Cyprus', '+357 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('9fe5446c-662b-45b6-a96f-9c74eca260ad', 'UA', 'UA +380', '+380', 'Ukraine', '+380 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('9feff50f-4194-4624-8391-04aef0bdbf7f', 'JE', 'JE +44', '+44', 'Jersey', '+44 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a0489b7c-c740-4906-91a5-cae57e02ff98', 'TZ', 'TZ +255', '+255', 'Tanzania', '+255 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a0854f22-2e32-4028-877d-e6697407cece', 'ZA', 'ZA +27', '+27', 'South Africa', '+27 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a09b8524-1c9b-4325-9278-b3b3d31a8b87', 'NZ', 'NZ +64', '+64', 'New Zealand', '+64 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a1717c67-44c7-431f-a2cf-623b53352a8e', 'BD', 'BD +880', '+880', 'Bangladesh', '+880 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a1e55ca0-c7cd-4b65-9c8b-4974963fec5c', 'BG', 'BG +359', '+359', 'Bulgaria', '+359 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a3cc54ed-46bb-4e3b-8cc0-7fdcbbfe835f', 'OM', 'OM +968', '+968', 'Oman', '+968 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a66d0c87-bbfe-416a-be71-5fc39145c5d9', 'BR', 'BR +55', '+55', 'Brazil', '+55 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a6eee62d-a8a2-48de-be4b-44c55b174167', 'NO', 'NO +47', '+47', 'Norway', '+47 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a76066aa-4cdf-4d15-ae5f-c51be60294d4', 'KZ', 'KZ +77', '+77', 'Kazakhstan', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('a7e59386-0bf6-42be-803c-52b691c784b7', 'VC', 'VC +1784', '+1784', 'Saint Vincent and the Grenadines', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('a867b30d-5672-4ccc-b12f-1e7abdd25fc7', 'LA', 'LA +856', '+856', 'Laos', '+856 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('a9cd3fe4-b580-4108-941a-774e695bcb4a', 'YE', 'YE +967', '+967', 'Yemen', '+967 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('aaf5bc0b-1f65-4a9b-a3e7-14ed51f9d3f8', 'DO', 'DO +1829', '+1829', 'Dominican Republic', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('ab31edd4-9ef6-44c9-be49-0918caf74c3b', 'NU', 'NU +683', '+683', 'Niue', '+683 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('acdac3a3-f7c4-4105-b5f4-60ff7bc276c9', 'PE', 'PE +51', '+51', 'Peru', '+51 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ae0899b4-2461-4f66-b75b-8ae4b02d5dd7', 'SY', 'SY +963', '+963', 'Syria', '+963 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ae669915-e4a3-49db-a1ea-62c89f21cff7', 'YT', 'YT +262', '+262', 'Mayotte', '+262 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('aeb208ce-7a3f-4e93-b046-3d408f0ccc17', 'VE', 'VE +58', '+58', 'Venezuela', '+58 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('aeeb222f-98ba-4c2f-80a2-a6698491f9c0', 'ME', 'ME +382', '+382', 'Montenegro', '+382 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('af71f929-2f59-4712-9187-764429bd9def', 'PK', 'PK +92', '+92', 'Pakistan', '+92 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('affa3981-4ea0-4d47-9296-3ba6a00dcee5', 'BN', 'BN +673', '+673', 'Brunei', '+673 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b01b8f01-f543-4b4c-b332-eedc678e90ab', 'GQ', 'GQ +240', '+240', 'Equatorial Guinea', '+240 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b0ae9513-7622-43f5-b08e-fba6b43f20da', 'CR', 'CR +506', '+506', 'Costa Rica', '+506 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b0dfaabc-0382-4b6c-8f02-f3fcd79206e8', 'KM', 'KM +269', '+269', 'Comoros', '+269 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b2ab24d9-953d-47ad-b788-2dbaa6462bdb', 'LY', 'LY +218', '+218', 'Libya', '+218 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b32d6364-b76c-47ec-8baa-f62c28ef674a', 'CW', 'CW +599', '+599', 'Curaçao', '+599 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b356cea1-9df7-4611-a78b-c32b2fd21597', 'BO', 'BO +591', '+591', 'Bolivia', '+591 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b36f16be-ab1e-4abd-9f3b-debbf243bffa', 'ET', 'ET +251', '+251', 'Ethiopia', '+251 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b39d99f7-cdeb-49fb-8ba3-7d91265694fa', 'RU', 'RU +73', '+73', 'Russia', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('b572be68-ce3f-419a-956a-96e73775be76', 'GT', 'GT +502', '+502', 'Guatemala', '+502 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b6b5df8b-0ee1-4841-94f6-2b4c017a2375', 'CD', 'CD +243', '+243', 'DR Congo', '+243 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b7b94516-fd84-453c-85a8-b464daa7e088', 'AE', 'AE +971', '+971', 'United Arab Emirates', '+971 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b84716e2-ae3b-4650-be46-1c4b879d9697', 'MR', 'MR +222', '+222', 'Mauritania', '+222 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b8b4c7e9-a05d-4172-8548-54731bb15f75', 'GY', 'GY +592', '+592', 'Guyana', '+592 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b909f94d-6643-4d25-aa56-114ddddca2a0', 'CO', 'CO +57', '+57', 'Colombia', '+57 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('b98ba1d7-2a92-4ab6-a7f1-055275e559e6', 'CL', 'CL +56', '+56', 'Chile', '+56 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ba046ed9-60c4-465c-a471-94afb71ff0a7', 'AW', 'AW +297', '+297', 'Aruba', '+297 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ba103ae9-05c7-4b28-bbf3-4deb1ae2e719', 'GR', 'GR +30', '+30', 'Greece', '+30 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('bb3975ef-214e-4ec2-8a33-bda5413f978a', 'NF', 'NF +672', '+672', 'Norfolk Island', '+672 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('bd06fd3b-c602-4911-8040-2c07d0e28aae', 'WS', 'WS +685', '+685', 'Samoa', '+685 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('bdd72a6a-917c-42c4-8428-8fd26e9a24f2', 'AG', 'AG +1268', '+1268', 'Antigua and Barbuda', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('be4603ac-980c-4c68-8ac2-7e76e8e79592', 'SL', 'SL +232', '+232', 'Sierra Leone', '+232 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('be4a20e5-35bd-41cc-ae36-f08694f0c2fc', 'CH', 'CH +41', '+41', 'Switzerland', '+41 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c00ddc84-ae13-4493-8306-ad41ce1223bb', 'TL', 'TL +670', '+670', 'Timor-Leste', '+670 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c12501a8-a62d-4d69-8a6f-fb3777ef8f73', 'MX', 'MX +52', '+52', 'Mexico', '+52 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c24c2ab6-973e-4af9-bc7f-e5e94e25cb95', 'AR', 'AR +54', '+54', 'Argentina', '+54 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c2fac0b3-69e4-47c7-80ba-61cb6b916f3d', 'LK', 'LK +94', '+94', 'Sri Lanka', '+94 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c3353d1c-3448-400e-9291-e885de816abc', 'JM', 'JM +1658', '+1658', 'Jamaica', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('c471e2ad-a866-4087-b27a-e298f4513c8d', 'MZ', 'MZ +258', '+258', 'Mozambique', '+258 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c71c0aa4-0a9b-4c67-950e-21fc93137d29', 'BZ', 'BZ +501', '+501', 'Belize', '+501 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('c78368c2-57ec-4e8b-bada-2e2958aa3610', 'BS', 'BS +1242', '+1242', 'Bahamas', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('c7eafc40-4820-4661-95fb-b7de84758842', 'MK', 'MK +389', '+389', 'North Macedonia', '+389 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cb5b3c5c-8971-45b2-b380-b26a6b695881', 'IE', 'IE +353', '+353', 'Ireland', '+353 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cc4411bf-f096-487b-816e-4736a703457d', 'IL', 'IL +972', '+972', 'Israel', '+972 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cced6bb3-270d-4876-b9fa-ff7bfac93086', 'CA', 'CA +1', '+1', 'Canada', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('ce194c0d-e85b-44f8-9004-ebb3fc6c65b7', 'IS', 'IS +354', '+354', 'Iceland', '+354 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cf9d5895-764d-4568-b0e7-3d4d4c89f04c', 'HU', 'HU +36', '+36', 'Hungary', '+36 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cfd86892-1018-4e68-ac78-d715b4637445', 'CF', 'CF +236', '+236', 'Central African Republic', '+236 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('cfdb7266-b9c5-4b98-aeba-f7bcbadc04a5', 'GU', 'GU +1671', '+1671', 'Guam', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('d123a3eb-4fd0-43c7-90e8-c6f8d42b4a39', 'DK', 'DK +45', '+45', 'Denmark', '+45 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('d1aecba3-fe25-4743-a375-f56c3e637265', 'SM', 'SM +378', '+378', 'San Marino', '+378 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('d308234d-51dc-450f-b45b-8e0df605a204', 'KP', 'KP +850', '+850', 'North Korea', '+850 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('d39b491d-08e8-4702-a2d2-cde3b85c20b7', 'TT', 'TT +1868', '+1868', 'Trinidad and Tobago', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('d6075a0d-0a57-4635-ba6b-3f3cc2b671d8', 'RU', 'RU +75', '+75', 'Russia', '+7 (###) ###-##-##', NULL, NULL, NULL, NULL, NULL, NULL),
('d672d7b5-d55a-45b2-b1a4-11f7ca3b7447', 'CV', 'CV +238', '+238', 'Cape Verde', '+238 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('d68b29af-2517-4473-b89c-c9cf1d15f8ef', 'CN', 'CN +86', '+86', 'China', '+86 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('d7757833-9c19-4e3c-b744-a6e053e82cb8', 'KY', 'KY +1345', '+1345', 'Cayman Islands', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('d8f6ed74-3a90-4991-9c76-93daf4ab7c00', 'BB', 'BB +1246', '+1246', 'Barbados', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('d96ced36-2535-440d-aff3-041a0e86b15f', 'MY', 'MY +60', '+60', 'Malaysia', '+60 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('da229fab-f417-4b59-8000-b06dcd62f039', 'PN', 'PN +64', '+64', 'Pitcairn Islands', '+64 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('da57e68b-eef6-4b45-a4d1-d9ddb367d25c', 'PW', 'PW +680', '+680', 'Palau', '+680 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('dbfac4fe-0c9a-4e81-9d3e-00a32d5fd7a1', 'HN', 'HN +504', '+504', 'Honduras', '+504 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('dc1f878d-1ea6-41e7-802e-cd0bf3b21054', 'AL', 'AL +355', '+355', 'Albania', '+355 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e01831bb-4275-4975-9a08-6e18c8138063', 'CI', 'CI +225', '+225', 'Ivory Coast', '+225 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e0f4ec83-290d-48d5-b7b0-0fe10cd1d027', 'US', 'US +1', '+1', 'United States', '(###) ###-####', NULL, NULL, '2025-07-11 17:49:55', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('e2a3d229-de87-4530-a14f-3fef04eb83db', 'NL', 'NL +31', '+31', 'Netherlands', '+31 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e42f8984-a2ab-4dc1-940e-580adc718c84', 'DZ', 'DZ +213', '+213', 'Algeria', '+213 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e617c476-5814-439d-bb90-cd3f8582d853', 'SR', 'SR +597', '+597', 'Suriname', '+597 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e6a61cf5-2baf-4749-b23a-5c37bcebab5c', 'MO', 'MO +853', '+853', 'Macau', '+853 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e6a8e1cc-3cb9-426d-8336-677f66b65036', 'MH', 'MH +692', '+692', 'Marshall Islands', '+692 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e75d2b54-60c5-436f-b8e5-d829351fe89b', 'BI', 'BI +257', '+257', 'Burundi', '+257 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('e966f156-2913-49d0-a30f-117b3d9c6ad9', 'MA', 'MA +212', '+212', 'Morocco', '+212 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ea9c731b-8cb5-4998-88d4-6f3782867ba2', 'EC', 'EC +593', '+593', 'Ecuador', '+593 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('eadae9d1-f8c3-4c1e-9832-20547c77cc7b', 'NE', 'NE +227', '+227', 'Niger', '+227 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ed3cbbc7-7825-41a1-87e6-ee76d611987d', 'BH', 'BH +973', '+973', 'Bahrain', '+973 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ee0f8afd-2967-4a8b-9368-7c40e89f506b', 'MP', 'MP +1670', '+1670', 'Northern Mariana Islands', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('ef1524ef-4de9-48ce-b6c5-36e443081955', 'LU', 'LU +352', '+352', 'Luxembourg', '+352 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ef164c57-fb37-4530-b6cd-da0898ebbd2c', 'GI', 'GI +350', '+350', 'Gibraltar', '+350 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ef96fd8f-8dea-44b7-961d-57c8ff026a86', 'TN', 'TN +216', '+216', 'Tunisia', '+216 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f033e969-e87f-462d-b3e4-56c59b8c60a2', 'NI', 'NI +505', '+505', 'Nicaragua', '+505 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f2bfe512-fc71-4f9a-9038-6426e60e5f71', 'LB', 'LB +961', '+961', 'Lebanon', '+961 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f3e62764-054e-415b-9024-1f52e220c21e', 'ML', 'ML +223', '+223', 'Mali', '+223 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f4e21264-6324-445d-b858-234b75adac56', 'LI', 'LI +423', '+423', 'Liechtenstein', '+423 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f5a2bd5b-9bcc-4c20-a128-3bd79708f4d8', 'LT', 'LT +370', '+370', 'Lithuania', '+370 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f5f82d9c-c855-47ab-ae17-93248eea8603', 'SB', 'SB +677', '+677', 'Solomon Islands', '+677 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f6d24b6d-b90f-4d01-afc0-95910ae9df78', 'NA', 'NA +264', '+264', 'Namibia', '+264 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f7bf610f-68f3-492d-a991-99b0354a83e9', 'BY', 'BY +375', '+375', 'Belarus', '+375 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f7d6020c-faa4-4d91-ba70-14d54af7fa48', 'FJ', 'FJ +679', '+679', 'Fiji', '+679 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f87e2a0a-23f4-40fd-ad50-7c14b4ca1dd5', 'DE', 'DE +49', '+49', 'Germany', '+49 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f96c3c78-0671-4791-9a76-5bfb0e3df8d9', 'BF', 'BF +226', '+226', 'Burkina Faso', '+226 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('f9a27e1f-e658-4128-b9da-dd674280a145', 'RW', 'RW +250', '+250', 'Rwanda', '+250 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('fa86b3a7-ae9c-4133-9059-36e201564694', 'GM', 'GM +220', '+220', 'Gambia', '+220 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('fb73176a-efe9-4fe8-b7f6-0cef3f347199', 'LC', 'LC +1758', '+1758', 'Saint Lucia', '+1 (###) ###-####', NULL, NULL, NULL, NULL, NULL, NULL),
('fe50099f-90d6-4636-ba2e-b6bfcaf01c8a', 'CK', 'CK +682', '+682', 'Cook Islands', '+682 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('fe91cf71-9167-4271-9f87-ad54c964ba18', 'SO', 'SO +252', '+252', 'Somalia', '+252 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ff8338e5-82dc-49bb-9efc-556c0f5b2ae2', 'KH', 'KH +855', '+855', 'Cambodia', '+855 ##########', NULL, NULL, NULL, NULL, NULL, NULL),
('ffa605f1-ebb2-4f49-97d7-0744f03ea06e', 'BQ', 'BQ +599', '+599', 'Caribbean Netherlands', '+599 ##########', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Disparadores `countries`
--
DELIMITER $$
CREATE TRIGGER `trg_countries_delete` BEFORE DELETE ON `countries` FOR EACH ROW BEGIN
  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'countries', OLD.country_id, 'DELETE_PHYSICAL', @user_id,
    @full_name, @user_type, NOW(), @action_timezone,
    NULL,
    JSON_OBJECT(
      'suffix', OLD.suffix,
      'full_prefix', OLD.full_prefix,
      'normalized_prefix', OLD.normalized_prefix,
      'country_name', OLD.country_name,
      'phone_mask', OLD.phone_mask,
      'created_at', OLD.created_at,
      'created_by', OLD.created_by,
      'deleted_at', OLD.deleted_at,
      'deleted_by', OLD.deleted_by
    ),
    @client_ip, @client_hostname, @user_agent,
    @client_os, @client_browser,
    @domain_name, @request_uri, @server_hostname,
    @client_country, @client_region, @client_city,
    @client_zipcode, @client_coordinates,
    @geo_ip_timestamp, @geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_countries_delete_logical` AFTER UPDATE ON `countries` FOR EACH ROW BEGIN
  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'countries', OLD.country_id, 'DELETE_LOGICAL', @user_id,
      @full_name, @user_type, NOW(), @action_timezone,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      JSON_OBJECT(
        'country_id', OLD.country_id,
        'suffix', OLD.suffix,
        'full_prefix', OLD.full_prefix,
        'normalized_prefix', OLD.normalized_prefix,
        'country_name', OLD.country_name,
        'phone_mask', OLD.phone_mask,
        'created_at', OLD.created_at,
        'created_by', OLD.created_by,
        'updated_at', OLD.updated_at,
        'updated_by', OLD.updated_by,
        'deleted_at', NEW.deleted_at,
        'deleted_by', NEW.deleted_by
      ),
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname,
      @client_country, @client_region, @client_city,
      @client_zipcode, @client_coordinates,
    @geo_ip_timestamp, @geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_countries_insert` AFTER INSERT ON `countries` FOR EACH ROW BEGIN
  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    full_name, user_type, action_timestamp, action_timezone,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname,
    client_country, client_region, client_city,
    client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
  ) VALUES (
    'countries', NEW.country_id, 'INSERT', @user_id,
    @full_name, @user_type, NOW(), @action_timezone,
    NULL,
    JSON_OBJECT(
      'suffix', NEW.suffix,
      'full_prefix', NEW.full_prefix,
      'normalized_prefix', NEW.normalized_prefix,
      'country_name', NEW.country_name,
      'phone_mask', NEW.phone_mask,
      'created_at', NEW.created_at,
      'created_by', NEW.created_by
    ),
    @client_ip, @client_hostname, @user_agent,
    @client_os, @client_browser,
    @domain_name, @request_uri, @server_hostname,
    @client_country, @client_region, @client_city,
    @client_zipcode, @client_coordinates,
    @geo_ip_timestamp, @geo_ip_timezone
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_countries_update` AFTER UPDATE ON `countries` FOR EACH ROW BEGIN
  DECLARE change_data TEXT;
  SET change_data = '{';

  IF OLD.suffix <> NEW.suffix THEN
    SET change_data = CONCAT(change_data, '"suffix":{"old":"', OLD.suffix, '","new":"', NEW.suffix, '"},');
  END IF;
  IF OLD.full_prefix <> NEW.full_prefix THEN
    SET change_data = CONCAT(change_data, '"full_prefix":{"old":"', OLD.full_prefix, '","new":"', NEW.full_prefix, '"},');
  END IF;
  IF OLD.normalized_prefix <> NEW.normalized_prefix THEN
    SET change_data = CONCAT(change_data, '"normalized_prefix":{"old":"', OLD.normalized_prefix, '","new":"', NEW.normalized_prefix, '"},');
  END IF;
  IF OLD.country_name <> NEW.country_name THEN
    SET change_data = CONCAT(change_data, '"country_name":{"old":"', OLD.country_name, '","new":"', NEW.country_name, '"},');
  END IF;
  IF OLD.phone_mask <> NEW.phone_mask THEN
    SET change_data = CONCAT(change_data, '"phone_mask":{"old":"', OLD.phone_mask, '","new":"', NEW.phone_mask, '"},');
  END IF;

  IF RIGHT(change_data, 1) = ',' THEN
    SET change_data = LEFT(change_data, LENGTH(change_data) - 1);
  END IF;

  SET change_data = CONCAT(change_data, '}');

  IF change_data <> '{}' THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      full_name, user_type, action_timestamp, action_timezone,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname,
      client_country, client_region, client_city,
      client_zipcode, client_coordinates,
    geo_ip_timestamp, geo_ip_timezone
    ) VALUES (
      'countries', OLD.country_id, 'UPDATE', @user_id,
      @full_name, @user_type, NOW(), @action_timezone,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname,
      @client_country, @client_region, @client_city,
      @client_zipcode, @client_coordinates,
    @geo_ip_timestamp, @geo_ip_timezone
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customers`
--

CREATE TABLE `customers` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `source` enum('BigSteelBox','EVOLVE') NOT NULL,
  `customer_type` enum('company','individual') NOT NULL,
  `company_name` varchar(160) DEFAULT NULL,
  `contact_first_name` varchar(80) DEFAULT NULL,
  `contact_last_name` varchar(80) DEFAULT NULL,
  `first_name` varchar(80) DEFAULT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventory`
--

CREATE TABLE `inventory` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `container_number` char(11) NOT NULL,
  `iso_size_type_code` char(4) DEFAULT NULL,
  `length_ft` decimal(5,2) DEFAULT NULL,
  `width_ft` decimal(4,2) DEFAULT NULL,
  `height_ft` decimal(4,2) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `configuration` enum('regular','DBE','45D','45D/DBE','other') DEFAULT NULL,
  `container_type` enum('standard','high_cube','office','reefer','other') NOT NULL DEFAULT 'standard',
  `condition` enum('new','cargo_worthy','wind_water_tight','as_is') NOT NULL DEFAULT 'wind_water_tight',
  `status` enum('available','reserved','on_rent','in_transit','maintenance','retired') NOT NULL DEFAULT 'available',
  `location_province_id` char(36) DEFAULT NULL,
  `location_city_id` char(36) DEFAULT NULL,
  `last_known_latitude` decimal(9,6) DEFAULT NULL,
  `last_known_longitude` decimal(9,6) DEFAULT NULL,
  `last_known_at` datetime DEFAULT NULL,
  `ownership` enum('owned','leased') NOT NULL DEFAULT 'owned',
  `owner_label` varchar(50) DEFAULT NULL,
  `offering` enum('rental','sale','both') DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `csc_valid_until` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventory`
--

INSERT INTO `inventory` (`id`, `container_number`, `iso_size_type_code`, `length_ft`, `width_ft`, `height_ft`, `color`, `configuration`, `container_type`, `condition`, `status`, `location_province_id`, `location_city_id`, `last_known_latitude`, `last_known_longitude`, `last_known_at`, `ownership`, `owner_label`, `offering`, `purchase_date`, `csc_valid_until`, `notes`, `created_at`, `updated_at`) VALUES
('0ad4a3f5-810d-11f0-bd4d-41b8da1d00a8', 'BSBU2864375', '42G1', 40.00, 8.00, 8.50, 'White', 'regular', 'standard', 'wind_water_tight', 'available', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', '0ac618fe-810d-11f0-bd4d-41b8da1d00a8', NULL, NULL, NULL, 'owned', 'BSB', 'rental', NULL, NULL, '40 ft standard GP; BSB-owned', '2025-08-24 13:09:06', '2025-08-24 13:09:06'),
('0ad4a92d-810d-11f0-bd4d-41b8da1d00a8', 'BSBU2299512', '22G1', 20.00, 8.00, 8.50, 'White', 'regular', 'standard', 'wind_water_tight', 'available', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', '0ac618fe-810d-11f0-bd4d-41b8da1d00a8', NULL, NULL, NULL, 'owned', 'EVOLVE', 'rental', NULL, NULL, '20 ft standard GP; EVOLVE-owned', '2025-08-24 13:09:06', '2025-08-24 13:09:06'),
('0ad4aae6-810d-11f0-bd4d-41b8da1d00a8', 'BSBU4367781', '42G1', 40.00, 8.00, 8.50, 'White', 'DBE', 'standard', 'cargo_worthy', 'maintenance', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', '0ac618fe-810d-11f0-bd4d-41b8da1d00a8', NULL, NULL, NULL, 'owned', 'EVOLVE', 'sale', NULL, NULL, '40 ft GP with double-doors both ends (DBE)', '2025-08-24 13:09:06', '2025-08-24 13:09:06'),
('0ad4ac58-810d-11f0-bd4d-41b8da1d00a8', 'BSBU6388892', '45G1', 40.00, 8.00, 9.50, 'White', '45D/DBE', 'high_cube', 'cargo_worthy', 'reserved', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', '0ac618fe-810d-11f0-bd4d-41b8da1d00a8', NULL, NULL, NULL, 'owned', 'BSB', 'rental', NULL, NULL, '40 ft High-Cube (9\'6\")', '2025-08-24 13:09:06', '2025-08-24 13:09:06'),
('0ad4adb0-810d-11f0-bd4d-41b8da1d00a8', 'BSBU2648837', '22G1', 20.00, 8.00, 8.50, 'Beige', 'regular', 'standard', 'wind_water_tight', 'available', '0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', '0ac618fe-810d-11f0-bd4d-41b8da1d00a8', NULL, NULL, NULL, 'owned', 'BSB', 'sale', NULL, NULL, '20 ft standard GP', '2025-08-24 13:09:06', '2025-08-24 13:09:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `password_reset_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provinces`
--

CREATE TABLE `provinces` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `provinces`
--

INSERT INTO `provinces` (`id`, `code`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
('0aaf86a2-810d-11f0-bd4d-41b8da1d00a8', 'BC', 'British Columbia', 1, '2025-08-24 13:09:06', '2025-08-24 13:09:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `security_questions`
--

CREATE TABLE `security_questions` (
  `security_question_id` char(36) NOT NULL,
  `user_id_user` char(36) DEFAULT NULL,
  `question1` varchar(255) NOT NULL,
  `answer1` varchar(255) NOT NULL,
  `question2` varchar(255) NOT NULL,
  `answer2` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `security_questions`
--

INSERT INTO `security_questions` (`security_question_id`, `user_id_user`, `question1`, `answer1`, `question2`, `answer2`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('2ee70414-5e80-4f3c-be52-ff523cc38c42', '3072b979-43a9-4640-a473-5650c4a82d54', 'Color favorito', 'Azul', 'Numero Favorito', '08', '2025-07-08 05:47:43', '3072b979-43a9-4640-a473-5650c4a82d54', '2025-07-15 13:02:51', '3072b979-43a9-4640-a473-5650c4a82d54', NULL, NULL),
('cb02618c-d46f-49a5-b879-15d2f70fd6db', NULL, 'Color favorito', 'Azul', 'Numero Favorito', '07', '2025-07-17 06:54:37', 'fdf23cb0-86f1-4902-85e3-c20a1f481835', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `session_config`
--

CREATE TABLE `session_config` (
  `config_id` int(11) NOT NULL COMMENT 'ID único de configuración de sesión',
  `timeout_minutes` int(11) NOT NULL DEFAULT 30 COMMENT 'Tiempo de expiración de sesión en minutos',
  `allow_ip_change` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Permitir cambio de IP durante sesión (0 = No, 1 = Sí)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Configuración global de la gestión de sesiones del sistema';

--
-- Volcado de datos para la tabla `session_config`
--

INSERT INTO `session_config` (`config_id`, `timeout_minutes`, `allow_ip_change`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 15, 0, '2025-08-05 17:01:19', NULL, '2025-08-05 17:04:33', '4de32685-d034-4a1f-8e66-2e48535593a4', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `session_management`
--

CREATE TABLE `session_management` (
  `session_id` char(36) NOT NULL DEFAULT uuid() COMMENT 'ID único de la sesión de usuario',
  `user_id` char(36) DEFAULT NULL COMMENT 'ID del usuario que inició sesión',
  `user_name` varchar(100) DEFAULT NULL COMMENT 'Nombre de usuario que inició sesión',
  `user_type` enum('administrator','user') DEFAULT NULL COMMENT 'Tipo de usuario (rol)',
  `full_name` varchar(100) DEFAULT NULL COMMENT 'Nombre completo del usuario',
  `login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `logout_time` datetime DEFAULT NULL,
  `inactivity_duration` varchar(255) DEFAULT NULL COMMENT 'Duración de inactividad antes del cierre',
  `login_success` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Indica si el login fue exitoso (1) o fallido (0)',
  `failure_reason` varchar(255) DEFAULT NULL COMMENT 'Motivo de falla en el inicio de sesión (si aplica)',
  `session_status` enum('active','closed','expired','failed','kicked') NOT NULL DEFAULT 'active' COMMENT 'Estado actual de la sesión',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'Dirección IP del cliente',
  `city` varchar(100) DEFAULT NULL COMMENT 'Ciudad detectada por geolocalización',
  `region` varchar(100) DEFAULT NULL COMMENT 'Región detectada por geolocalización',
  `country` varchar(100) DEFAULT NULL COMMENT 'País detectado por geolocalización',
  `zipcode` varchar(20) DEFAULT NULL COMMENT 'Código postal detectado',
  `coordinates` varchar(50) DEFAULT NULL COMMENT 'Coordenadas geográficas (latitud,longitud)',
  `hostname` varchar(100) DEFAULT NULL COMMENT 'Nombre del host del cliente',
  `os` varchar(100) DEFAULT NULL COMMENT 'Sistema operativo del cliente',
  `browser` varchar(100) DEFAULT NULL COMMENT 'Navegador del cliente',
  `user_agent` text DEFAULT NULL COMMENT 'Cadena completa del agente de usuario',
  `device_id` varchar(100) DEFAULT NULL COMMENT 'ID del dispositivo usado para la sesión',
  `device_type` tinyint(1) DEFAULT NULL COMMENT 'Tipo de dispositivo (0 = desktop, 1 = móvil, etc.)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` char(36) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` char(36) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Gestión de sesiones activas e históricas de usuarios del sistema';

--
-- Volcado de datos para la tabla `session_management`
--

INSERT INTO `session_management` (`session_id`, `user_id`, `user_name`, `user_type`, `full_name`, `login_time`, `logout_time`, `inactivity_duration`, `login_success`, `failure_reason`, `session_status`, `ip_address`, `city`, `region`, `country`, `zipcode`, `coordinates`, `hostname`, `os`, `browser`, `user_agent`, `device_id`, `device_type`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('59881ffe-f84b-4b48-b8a6-a2f29199dfe6', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', '', 'Moises Celis', '2025-08-24 01:12:50', NULL, NULL, 1, NULL, 'active', '::1', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', 'DESKTOP-92VMM39', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', NULL, NULL, '2025-08-24 08:12:50', NULL, '2025-08-24 01:12:50', NULL, NULL, NULL),
('bbc31394-23ec-4136-b48a-699ed1afb84f', '3072b979-43a9-4640-a473-5650c4a82d54', 'moisescelis21@gmail.com', 'user', 'Moises Celis', '2025-08-24 01:15:03', NULL, NULL, 0, 'invalid_password', 'failed', '::1', 'Unknown', 'Unknown', 'Unknown', 'Unknown', '0.0,0.0', 'DESKTOP-92VMM39', 'Windows 10', 'Google Chrome', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', NULL, NULL, '2025-08-24 08:15:03', NULL, '2025-08-24 01:15:03', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `user_id` char(36) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `timezone` varchar(255) DEFAULT 'America/Los_Angeles',
  `status` int(255) NOT NULL DEFAULT 1,
  `rol` enum('administrator','user') NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `sex`, `email`, `password`, `telephone`, `timezone`, `status`, `rol`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
('2ea94ca9-90b0-40b4-a119-a1dd60154828', 'Jesus', 'Zapatin', 'f', 'jesusmadafaka13@gmail.com', '$2y$12$I4WcQeXi3tV8fqvnz/xQse3qXRPd/haXv.HweGCEdgf/8mabXGmnW', '(+58) 4249541158', 'America/Los_Angeles', 1, 'administrator', '2025-06-25 09:57:08', NULL, '2025-06-25 10:40:34', '211', NULL, NULL),
('3072b979-43a9-4640-a473-5650c4a82d54', 'Moises', 'Celis', 'm', 'moisescelis21@gmail.com', '$2y$12$jLElDEEtOPGuLZCw3lMy/u00pFkEb/MmBkuJXYODe9R.2WqN375HK', '(+1) (626)423-8692', 'America/Los_Angeles', 1, 'administrator', NULL, NULL, '2025-07-16 20:03:50', '3a3963c7-a08e-44b9-9a89-7081a04b2c42', NULL, NULL),
('34e023d4-5339-41bc-a6ee-ed8cfcbebb77', 'Jesús', 'Zapata', 'm', 'jesuszapata@gmail.com', '$2y$12$YNYYHZp5MR88iJCAjbCjBuxkrlHVxQmdeWFbgS96jOohTuA9d.p2W', '(+1) 6264238682', 'America/Los_Angeles', 1, 'user', NULL, NULL, NULL, NULL, NULL, NULL),
('acfa725e-561d-4911-afeb-0f35b7323b6d', 'Alejandro', 'S', 'm', 'marcelrojas@hotmail.es', '$2y$12$Xs91ga0pQgJk5ZrJn8flx.4ngNkBNeKZuZKBi.TOUkQUiYptwwKjq', '(+1) 6264238682', 'America/Los_Angeles', 1, 'user', NULL, NULL, NULL, NULL, NULL, NULL),
('d3aa1ffb-7dd6-4397-a1ae-38798890a585', 'Alejandro', 'Rojas', 'm', 'marcel85rs@gmail.com', '$2y$12$O2GVTgxEN8lm.uNNPAgyVOrOrIpWO6y0z6Q799ajpM7hx75.vLRRm', '(+1) (626)423-8682', 'America/Los_Angeles', 1, 'administrator', NULL, NULL, '2025-06-13 19:22:21', '205', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_available_by_size_type`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_available_by_size_type` (
`iso_size_type_code` char(4)
,`container_type` enum('standard','high_cube','office','reefer','other')
,`city` varchar(120)
,`province` varchar(10)
,`available_units` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_inventory_status_counts`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_inventory_status_counts` (
`status` enum('available','reserved','on_rent','in_transit','maintenance','retired')
,`city` varchar(120)
,`province` varchar(10)
,`units` bigint(21)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workers`
--

CREATE TABLE `workers` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `role` varchar(80) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `address_line1` varchar(160) DEFAULT NULL,
  `address_line2` varchar(160) DEFAULT NULL,
  `province_id` char(36) DEFAULT NULL,
  `city_id` char(36) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `hired_at` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `work_orders`
--

CREATE TABLE `work_orders` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `customer_id` char(36) NOT NULL,
  `type` enum('pickup','delivery','relocation','storage_in','storage_out') NOT NULL,
  `scheduled_pickup_at` datetime DEFAULT NULL,
  `scheduled_delivery_at` datetime DEFAULT NULL,
  `requested_quantity` int(11) NOT NULL DEFAULT 1,
  `origin_address_line1` varchar(160) DEFAULT NULL,
  `origin_address_line2` varchar(160) DEFAULT NULL,
  `origin_province_id` char(36) DEFAULT NULL,
  `origin_city_id` char(36) DEFAULT NULL,
  `origin_postal_code` varchar(20) DEFAULT NULL,
  `dest_address_line1` varchar(160) DEFAULT NULL,
  `dest_address_line2` varchar(160) DEFAULT NULL,
  `dest_province_id` char(36) DEFAULT NULL,
  `dest_city_id` char(36) DEFAULT NULL,
  `dest_postal_code` varchar(20) DEFAULT NULL,
  `assigned_worker_id` char(36) DEFAULT NULL,
  `status` enum('draft','scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `work_order_events`
--

CREATE TABLE `work_order_events` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `work_order_id` char(36) NOT NULL,
  `work_order_item_id` char(36) DEFAULT NULL,
  `event_type` enum('pickup_confirmed','delivery_confirmed','storage_in','storage_out','arrival','departure','location_update') NOT NULL,
  `event_time` datetime NOT NULL DEFAULT current_timestamp(),
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `accuracy_meters` decimal(6,2) DEFAULT NULL,
  `captured_by_worker_id` char(36) DEFAULT NULL,
  `captured_via` enum('mobile','web','api') NOT NULL DEFAULT 'mobile',
  `captured_ip` varchar(45) DEFAULT NULL,
  `device_id` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `work_order_items`
--

CREATE TABLE `work_order_items` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `work_order_id` char(36) NOT NULL,
  `inventory_id` char(36) NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_available_by_size_type`
--
DROP TABLE IF EXISTS `v_available_by_size_type`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_available_by_size_type`  AS SELECT `i`.`iso_size_type_code` AS `iso_size_type_code`, `i`.`container_type` AS `container_type`, `c`.`name` AS `city`, `p`.`code` AS `province`, count(0) AS `available_units` FROM ((`inventory` `i` left join `cities` `c` on(`c`.`id` = `i`.`location_city_id`)) left join `provinces` `p` on(`p`.`id` = `i`.`location_province_id`)) WHERE `i`.`status` = 'available' GROUP BY `i`.`iso_size_type_code`, `i`.`container_type`, `c`.`name`, `p`.`code` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_inventory_status_counts`
--
DROP TABLE IF EXISTS `v_inventory_status_counts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_inventory_status_counts`  AS SELECT `i`.`status` AS `status`, `c`.`name` AS `city`, `p`.`code` AS `province`, count(0) AS `units` FROM ((`inventory` `i` left join `cities` `c` on(`c`.`id` = `i`.`location_city_id`)) left join `provinces` `p` on(`p`.`id` = `i`.`location_province_id`)) GROUP BY `i`.`status`, `c`.`name`, `p`.`code` ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indices de la tabla `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_city` (`province_id`,`name`);

--
-- Indices de la tabla `contact_emails`
--
ALTER TABLE `contact_emails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_email_entity` (`entity_type`,`entity_id`,`email`),
  ADD KEY `ix_email_lookup` (`email`);

--
-- Indices de la tabla `contact_phones`
--
ALTER TABLE `contact_phones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ix_phone_entity` (`entity_type`,`entity_id`),
  ADD KEY `ix_phone_lookup` (`phone_number`);

--
-- Indices de la tabla `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`);

--
-- Indices de la tabla `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_container_number` (`container_number`),
  ADD KEY `ix_inventory_status` (`status`),
  ADD KEY `ix_inventory_loc` (`location_province_id`,`location_city_id`),
  ADD KEY `fk_inv_city` (`location_city_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`password_reset_id`),
  ADD KEY `idx_email` (`email`);

--
-- Indices de la tabla `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_province_code` (`code`);

--
-- Indices de la tabla `security_questions`
--
ALTER TABLE `security_questions`
  ADD PRIMARY KEY (`security_question_id`),
  ADD KEY `idx_user_id_user` (`user_id_user`);

--
-- Indices de la tabla `session_config`
--
ALTER TABLE `session_config`
  ADD PRIMARY KEY (`config_id`);

--
-- Indices de la tabla `session_management`
--
ALTER TABLE `session_management`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `fk_session_user` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indices de la tabla `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_worker_province` (`province_id`),
  ADD KEY `fk_worker_city` (`city_id`);

--
-- Indices de la tabla `work_orders`
--
ALTER TABLE `work_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_wo_customer` (`customer_id`),
  ADD KEY `fk_wo_worker` (`assigned_worker_id`),
  ADD KEY `fk_wo_origin_prov` (`origin_province_id`),
  ADD KEY `fk_wo_origin_city` (`origin_city_id`),
  ADD KEY `fk_wo_dest_prov` (`dest_province_id`),
  ADD KEY `fk_wo_dest_city` (`dest_city_id`);

--
-- Indices de la tabla `work_order_events`
--
ALTER TABLE `work_order_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_woe_woi` (`work_order_item_id`),
  ADD KEY `fk_woe_worker` (`captured_by_worker_id`),
  ADD KEY `ix_woe_wo` (`work_order_id`,`event_time`),
  ADD KEY `ix_woe_geo` (`latitude`,`longitude`);

--
-- Indices de la tabla `work_order_items`
--
ALTER TABLE `work_order_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_woi_unique` (`work_order_id`,`inventory_id`),
  ADD KEY `fk_woi_inv` (`inventory_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `audit_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID único del registro de auditoría', AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `password_reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `session_config`
--
ALTER TABLE `session_config`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único de configuración de sesión', AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `fk_city_province` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`);

--
-- Filtros para la tabla `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inv_city` FOREIGN KEY (`location_city_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `fk_inv_province` FOREIGN KEY (`location_province_id`) REFERENCES `provinces` (`id`);

--
-- Filtros para la tabla `workers`
--
ALTER TABLE `workers`
  ADD CONSTRAINT `fk_worker_city` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `fk_worker_province` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`);

--
-- Filtros para la tabla `work_orders`
--
ALTER TABLE `work_orders`
  ADD CONSTRAINT `fk_wo_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `fk_wo_dest_city` FOREIGN KEY (`dest_city_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `fk_wo_dest_prov` FOREIGN KEY (`dest_province_id`) REFERENCES `provinces` (`id`),
  ADD CONSTRAINT `fk_wo_origin_city` FOREIGN KEY (`origin_city_id`) REFERENCES `cities` (`id`),
  ADD CONSTRAINT `fk_wo_origin_prov` FOREIGN KEY (`origin_province_id`) REFERENCES `provinces` (`id`),
  ADD CONSTRAINT `fk_wo_worker` FOREIGN KEY (`assigned_worker_id`) REFERENCES `workers` (`id`);

--
-- Filtros para la tabla `work_order_events`
--
ALTER TABLE `work_order_events`
  ADD CONSTRAINT `fk_woe_wo` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`),
  ADD CONSTRAINT `fk_woe_woi` FOREIGN KEY (`work_order_item_id`) REFERENCES `work_order_items` (`id`),
  ADD CONSTRAINT `fk_woe_worker` FOREIGN KEY (`captured_by_worker_id`) REFERENCES `workers` (`id`);

--
-- Filtros para la tabla `work_order_items`
--
ALTER TABLE `work_order_items`
  ADD CONSTRAINT `fk_woi_inv` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`),
  ADD CONSTRAINT `fk_woi_wo` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
