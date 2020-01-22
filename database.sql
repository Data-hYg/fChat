--
-- This does not contain all requiered commands for fchat.
--

--
-- Table structure for table `chat_users`
--


CREATE TABLE `fchat`.`user` (
`userID` int(11) NOT NULL,
`username` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
`profilePic` varchar(255) NOT NULL,
`current_session` int(11) NOT NULL,
`status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chat_users`
--

INSERT INTO `fchat`.`user` (`userID`, `username`, `password`, `profilePic`, `current_session`, `status`) VALUES
(1, 'Timo', 'timoHey', 'timo.jpg', 1, 0),
(2, 'Alex', 'alexBae', 'alex.jpg', 2, 0),
(3, 'TestUser1', 'tstUsr1', 'test1.jpg', 1, 0),
(4, 'TestUser2', 'tstUsr2', 'user3.jpg', 2, 0);

--
-- Indexes for table `chat_users`
--
ALTER TABLE `fchat`.`chat_users`
  ADD PRIMARY KEY (`userID`);
  

--
-- Table structure for table `chat`
--

CREATE TABLE `fchat`.`chat` (
`chatID` int(11) NOT NULL,
`sender_userID` int(11) NOT NULL,
`reciever_userID` int(11) NOT NULL,
`message` text NOT NULL,
`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `chat`
--
ALTER TABLE `fchat`.`chat`
  ADD PRIMARY KEY (`chatid`);
  
  

--
-- Table structure for table `chat_login_details`
--

CREATE TABLE `fchat`.`chat_logging` (
`ID` int(11) NOT NULL,
`userID` int(11) NOT NULL,
`last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`is_typing` enum('no','yes') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Indexes for table `chat_login_details`
--
ALTER TABLE `fchat`.`chat_logging`
  ADD PRIMARY KEY (`ID`);
  
ALTER TABLE `fchat`.`chat` 
CHANGE COLUMN `chatID` `chatID` INT(11) NOT NULL AUTO_INCREMENT ;
  
ALTER TABLE `fchat`.`chat_logging` 
CHANGE COLUMN `ID` `ID` INT(11) NOT NULL AUTO_INCREMENT ;