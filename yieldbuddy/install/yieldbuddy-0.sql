-- phpMyAdmin SQL Dump
-- version 3.5.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 28, 2013 at 08:42 AM
-- Server version: 5.5.29-0ubuntu0.12.10.1
-- PHP Version: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yieldbuddy-0`
--

-- --------------------------------------------------------

--
-- Table structure for table `Arduino`
--

CREATE TABLE IF NOT EXISTS `Arduino` (
  `Time` text NOT NULL,
  `Month` int(11) NOT NULL,
  `Day` int(11) NOT NULL,
  `Year` int(11) NOT NULL,
  `Hour` int(11) NOT NULL,
  `Minute` int(11) NOT NULL,
  `Second` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Arduino`
--

INSERT INTO `Arduino` (`Time`, `Month`, `Day`, `Year`, `Hour`, `Minute`, `Second`) VALUES
('Mar 28 2013 08:42:43 AM', 3, 28, 2013, 8, 42, 43);

-- --------------------------------------------------------

--
-- Table structure for table `Camera`
--

CREATE TABLE IF NOT EXISTS `Camera` (
  `connectback_address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Camera`
--

INSERT INTO `Camera` (`connectback_address`) VALUES
('http://127.0.0.1:8081');

-- --------------------------------------------------------

--
-- Table structure for table `CO2`
--

CREATE TABLE IF NOT EXISTS `CO2` (
  `Low` float NOT NULL,
  `High` float NOT NULL,
  `CO2_ON` float NOT NULL,
  `CO2_OFF` float NOT NULL,
  `CO2_Enabled` int(11) NOT NULL,
  `Status` text NOT NULL,
  `Low_Alarm` int(11) NOT NULL,
  `Low_Time` text NOT NULL,
  `High_Alarm` int(11) NOT NULL,
  `High_Time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `CO2`
--

INSERT INTO `CO2` (`Low`, `High`, `CO2_ON`, `CO2_OFF`, `CO2_Enabled`, `Status`, `Low_Alarm`, `Low_Time`, `High_Alarm`, `High_Time`) VALUES
(60, 75, 40, 60, 0, 'LOW', 2, 'Mar 28 2013 08:31:09 AM', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `Email`
--

CREATE TABLE IF NOT EXISTS `Email` (
  `smtp_server` text NOT NULL,
  `smtp_port` text NOT NULL,
  `login_email_address` text NOT NULL,
  `password_hash` text NOT NULL,
  `recipient` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Email`
--

INSERT INTO `Email` (`smtp_server`, `smtp_port`, `login_email_address`, `password_hash`, `recipient`) VALUES
('smtp.gmail.com', '587', 'example@gmail.com', 'ef8658ce3cc0203352cd6007cf75626efe6fa25b8ce10e688c12f4931a033ef9e59d06f2544f41595191edf2831a241ff25dd8de5023fe6a6d7e536cae2d702c', 'example@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `Light`
--

CREATE TABLE IF NOT EXISTS `Light` (
  `Low` float NOT NULL,
  `High` float NOT NULL,
  `Status` text NOT NULL,
  `Low_Alarm` int(11) NOT NULL,
  `Low_Time` text NOT NULL,
  `High_Alarm` int(11) NOT NULL,
  `High_Time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Light`
--

INSERT INTO `Light` (`Low`, `High`, `Status`, `Low_Alarm`, `Low_Time`, `High_Alarm`, `High_Time`) VALUES
(20, 95, 'OK', 0, 'Mar 28 2013 05:30:56 AM', 0, 'Mar 25 2013 07:14:00 PM');

-- --------------------------------------------------------

--
-- Table structure for table `Lighting`
--

CREATE TABLE IF NOT EXISTS `Lighting` (
  `Light_ON_hour` int(11) NOT NULL,
  `Light_ON_min` int(11) NOT NULL,
  `Light_OFF_hour` int(11) NOT NULL,
  `Light_OFF_min` int(11) NOT NULL,
  `Low` float NOT NULL,
  `High` float NOT NULL,
  `Status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Lighting`
--

INSERT INTO `Lighting` (`Light_ON_hour`, `Light_ON_min`, `Light_OFF_hour`, `Light_OFF_min`, `Low`, `High`, `Status`) VALUES
(23, 30, 11, 30, 0, 0, 'TEST');

-- --------------------------------------------------------

--
-- Table structure for table `pH1`
--

CREATE TABLE IF NOT EXISTS `pH1` (
  `Low` float NOT NULL,
  `High` float NOT NULL,
  `Status` text NOT NULL,
  `Low_Alarm` int(11) NOT NULL,
  `Low_Time` text NOT NULL,
  `High_Alarm` int(11) NOT NULL,
  `High_Time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pH1`
--

INSERT INTO `pH1` (`Low`, `High`, `Status`, `Low_Alarm`, `Low_Time`, `High_Alarm`, `High_Time`) VALUES
(5.6, 6.1, 'HIGH', 2, 'Mar 28 2013 08:31:00 AM', 2, 'Mar 28 2013 08:30:31 AM');

-- --------------------------------------------------------

--
-- Table structure for table `pH2`
--

CREATE TABLE IF NOT EXISTS `pH2` (
  `Low` float NOT NULL,
  `High` float NOT NULL,
  `Status` text NOT NULL,
  `Low_Alarm` int(11) NOT NULL,
  `Low_Time` text NOT NULL,
  `High_Alarm` int(11) NOT NULL,
  `High_Time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pH2`
--

INSERT INTO `pH2` (`Low`, `High`, `Status`, `Low_Alarm`, `Low_Time`, `High_Alarm`, `High_Time`) VALUES
(5.6, 6.1, 'HIGH', 2, 'Mar 28 2013 05:30:39 AM', 2, 'Mar 28 2013 05:13:26 AM');

-- --------------------------------------------------------

--
-- Table structure for table `Relays`
--

CREATE TABLE IF NOT EXISTS `Relays` (
  `Relay1` int(11) NOT NULL,
  `Relay1_isAuto` int(11) NOT NULL,
  `Relay2` int(11) NOT NULL,
  `Relay2_isAuto` int(11) NOT NULL,
  `Relay3` int(11) NOT NULL,
  `Relay3_isAuto` int(11) NOT NULL,
  `Relay4` int(11) NOT NULL,
  `Relay4_isAuto` int(11) NOT NULL,
  `Relay5` int(11) NOT NULL,
  `Relay5_isAuto` int(11) NOT NULL,
  `Relay6` int(11) NOT NULL,
  `Relay6_isAuto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Relays`
--

INSERT INTO `Relays` (`Relay1`, `Relay1_isAuto`, `Relay2`, `Relay2_isAuto`, `Relay3`, `Relay3_isAuto`, `Relay4`, `Relay4_isAuto`, `Relay5`, `Relay5_isAuto`, `Relay6`, `Relay6_isAuto`) VALUES
(0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `RH`
--

CREATE TABLE IF NOT EXISTS `RH` (
  `Low` float NOT NULL,
  `High` float NOT NULL,
  `Humidifier_ON` float NOT NULL,
  `Humidifier_OFF` float NOT NULL,
  `Dehumidifier_ON` float NOT NULL,
  `Dehumidifier_OFF` float NOT NULL,
  `Status` text NOT NULL,
  `Low_Alarm` int(11) NOT NULL,
  `Low_Time` text NOT NULL,
  `High_Alarm` int(11) NOT NULL,
  `High_Time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `RH`
--

INSERT INTO `RH` (`Low`, `High`, `Humidifier_ON`, `Humidifier_OFF`, `Dehumidifier_ON`, `Dehumidifier_OFF`, `Status`, `Low_Alarm`, `Low_Time`, `High_Alarm`, `High_Time`) VALUES
(30, 80, 40, 60, 80, 60, 'LOW', 2, 'Mar 28 2013 08:31:03 AM', 0, 'Mar 26 2013 11:57:24 PM');

-- --------------------------------------------------------

--
-- Table structure for table `Sensors`
--

CREATE TABLE IF NOT EXISTS `Sensors` (
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pH1` float NOT NULL,
  `pH2` float NOT NULL,
  `Temp` float NOT NULL,
  `RH` float NOT NULL,
  `TDS1` float NOT NULL,
  `TDS2` float NOT NULL,
  `CO2` float NOT NULL,
  `Light` float NOT NULL,
  PRIMARY KEY (`Time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Sensors`
--

INSERT INTO `Sensors` (`Time`, `pH1`, `pH2`, `Temp`, `RH`, `TDS1`, `TDS2`, `CO2`, `Light`) VALUES
('2013-03-28 11:12:16', 11.18, 9.49, 23.64, 22, 0, 0, 0, 82.32);

-- --------------------------------------------------------

--
-- Table structure for table `Sensors_Log`
--

CREATE TABLE IF NOT EXISTS `Sensors_Log` (
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pH1` float NOT NULL,
  `pH2` float NOT NULL,
  `Temp` float NOT NULL,
  `RH` float NOT NULL,
  `TDS1` float NOT NULL,
  `TDS2` float NOT NULL,
  `CO2` float NOT NULL,
  `Light` float NOT NULL,
  PRIMARY KEY (`Time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TDS1`
--

CREATE TABLE IF NOT EXISTS `TDS1` (
  `Low` float NOT NULL,
  `High` float NOT NULL,
  `NutePump1_ON` float NOT NULL,
  `NutePump1_OFF` float NOT NULL,
  `MixPump1_Enabled` int(11) NOT NULL,
  `Status` text NOT NULL,
  `Low_Alarm` int(11) NOT NULL,
  `Low_Time` text NOT NULL,
  `High_Alarm` int(11) NOT NULL,
  `High_Time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `TDS1`
--

INSERT INTO `TDS1` (`Low`, `High`, `NutePump1_ON`, `NutePump1_OFF`, `MixPump1_Enabled`, `Status`, `Low_Alarm`, `Low_Time`, `High_Alarm`, `High_Time`) VALUES
(500, 800, 500, 800, 0, 'LOW', 2, 'Mar 28 2013 08:31:05 AM', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `TDS2`
--

CREATE TABLE IF NOT EXISTS `TDS2` (
  `Low` float NOT NULL,
  `High` float NOT NULL,
  `NutePump2_ON` float NOT NULL,
  `NutePump2_OFF` float NOT NULL,
  `MixPump2_Enabled` int(11) NOT NULL,
  `Status` text NOT NULL,
  `Low_Alarm` int(11) NOT NULL,
  `Low_Time` text NOT NULL,
  `High_Alarm` int(11) NOT NULL,
  `High_Time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `TDS2`
--

INSERT INTO `TDS2` (`Low`, `High`, `NutePump2_ON`, `NutePump2_OFF`, `MixPump2_Enabled`, `Status`, `Low_Alarm`, `Low_Time`, `High_Alarm`, `High_Time`) VALUES
(500, 800, 500, 800, 0, 'LOW', 2, 'Mar 28 2013 08:31:07 AM', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `Temp`
--

CREATE TABLE IF NOT EXISTS `Temp` (
  `Low` float NOT NULL,
  `High` float NOT NULL,
  `Heater_ON` float NOT NULL,
  `Heater_OFF` float NOT NULL,
  `AC_ON` float NOT NULL,
  `AC_OFF` float NOT NULL,
  `Status` text NOT NULL,
  `Low_Alarm` int(11) NOT NULL,
  `Low_Time` text NOT NULL,
  `High_Alarm` int(11) NOT NULL,
  `High_Time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Temp`
--

INSERT INTO `Temp` (`Low`, `High`, `Heater_ON`, `Heater_OFF`, `AC_ON`, `AC_OFF`, `Status`, `Low_Alarm`, `Low_Time`, `High_Alarm`, `High_Time`) VALUES
(21, 29, 21, 25, 29, 25, 'OK', 2, 'Mar 28 2013 06:45:01 AM', 0, '0');

-- --------------------------------------------------------

--
-- Table structure for table `Watering`
--

CREATE TABLE IF NOT EXISTS `Watering` (
  `Pump_start_hour` int(11) NOT NULL,
  `Pump_start_min` int(11) NOT NULL,
  `Pump_start_isAM` int(11) NOT NULL,
  `Pump_every_hours` int(11) NOT NULL,
  `Pump_every_mins` int(11) NOT NULL,
  `Pump_for` int(11) NOT NULL,
  `Pump_times` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Watering`
--

INSERT INTO `Watering` (`Pump_start_hour`, `Pump_start_min`, `Pump_start_isAM`, `Pump_every_hours`, `Pump_every_mins`, `Pump_for`, `Pump_times`) VALUES
(23, 45, 1, 2, 15, 15, 6);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
