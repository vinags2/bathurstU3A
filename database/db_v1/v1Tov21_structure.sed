#delete all views and temporary tables
44,119d;154,188d;240,266d;283,401d;433,505d;671,740d;761,772d;790,825d;844,1155d
#delete all the log file entries
/, 'logout',/d
/, 'navigation'/d
/, 'login',/d
/, 'logout',/d
/, 'DML',/d
/, 'DEBUG',/d
/INSERT INTO `log`/d
/, 'insert',/d
/Dumping data for table `logs`/d
/Structure for view/d
/DROP TABLE IF EXISTS/d
/REATE ALGORITHM=UNDEFINED/d
#
#convert all column and table names to lower case
s/`[^`]*`/\L&/g
#
#add comments
s/`primaryfacilitator` int(11) NOT NULL/`primaryfacilitator` int(11) NOT NULL COMMENT 'Primary facilitator'/g
s/`secondaryfacilitator` int(11) DEFAULT NULL/`secondaryfacilitator` int(11) NOT NULL COMMENT 'Secondary facilitator'/g
s/Pointer to row in People Table/Member/g
s/`idreports` int(11) NOT NULL/`idreports` int(11) NOT NULL COMMENT 'Not used any more'/g
#
#convert all primary keys to 'id'
s/idaddresses/id/g
s/idcourseattedancehistory/id/g
s/`idcourses`/`id`/g
s/idcoursesessions/id/g
s/`idlog`/`id`/g
s/`idmembershiphistory`/`id`/g
s/`idmenus`/`id`/g
s/`idpendingmembership`/`id`/g
s/`idpersons`/`id`/g
s/`idvenues`/`id`/g
s/`idreportcolumnwidths`/`id`/g
s/`idreportheadertitle`/`id`/g
s/`idreportpagename`/`id`/g
s/`idreportquerystring`/`id`/g
s/`idreports4mailinglist`/`id`/g
s/`idreportsql`/`id`/g
s/`idreporttabletolookup`/`id`/g
s/`idreports`/`id`/g
s/`idsettings`/`id`/g
s/`idcourseattendancehistory`/`id`/g
#
#convert all table names to snake_case and plural, and correctly name pivot tables
# as the singular name of the related tables listed in alphabetical order.
s/`courseattendancehistory/`attendance_histories/g
s/`courseattendees`/`session_person`/g
s/coursesessions/sessions/g
s/`log`/`logs`/g
s/`membershiphistory`/`membership_histories`/g
s/`pendingmembership`/`pending_memberships`/g
s/`reportcolumnwidths`/`report_column_widths`/g
s/`reportheadertitle`/`report_header_titles`/g
s/`reportpagename`/`report_page_names`/g
s/`reportquerystring`/`report_query_strings`/g
s/`reports4mailinglist`/`reports_mailing_lists`/g
s/`reportsql`/`report_sql`/g
s/`reporttabletolookup`/`report_tables_to_lookup`/g
s/`users`/`old_users_table`/g
s/`role` varchar(45) NOT NULL/`role` varchar(45) NOT NULL default '1'/g
#
#convert all columns to snake_case
s/companyname/company_name/g
s/addressline1/address_line_1/g
s/addressline2/address_line_2/g
s/line1/line_1/g
s/line2/line_2/g
s/updatedby/updated_by/g
s/updatedwhen/updated_at/g
s/`enroldate`/`enrolment_date`/g
s/`dateofenrolment`/`enrolment_date`/g
s/coursename/name/g
s/`onlyoneenrolmentform`/`only_one_enrolment_form`/g
s/`nolongerofferred`/`no_longer_offerred`/g
s/sessionname/name/g
s/dayofweek/day_of_week/g
s/starttime/start_time/g
s/endtime/end_time/g
s/followsterm/follows_term/g
s/rolltype/roll_type/g
s/first20weeks/first_20_weeks/g
s/second20weeks/second_20_weeks/g
s/last12weeks/last_12_weeks/g
s/maxattendees/maximum_class_size/g
s/termlength/term_length/g
s/minattendees/minimum_class_size/g
s/`logtype`/`type`/g
s/`description1`/`main_description`/g
s/`description2`/`secondary_description`/g
s/`member` int(11)/`person_id` int(11)/g
s/`joindate`/`join_date`/g
s/`receiptnumber`/`receipt_number`/g
s/`menuorder`/`menu_order`/g
s/`lastname`/`last_name`/g
s/`firstname`/`first_name`/g
s/`emergencylastname`/`emergency_last_name`/g
s/`emergencyfirstname`/`emergency_first_name`/g
s/`emergencyphone`/`emergency_phone`/g
s/`emergencymobile`/`emergency_mobile`/g
s/`emergencyemail`/`emergency_email`/g
s/`whensent`/`sent_at`/g
s/`venuename`/`name`/g
s/`contact` int(11) DEFAULT NULL/`contact` int(11) DEFAULT NULL COMMENT 'The contact of the venue'/g
s/`residentialaddress` int(11) DEFAULT NULL COMMENT 'Pointer to address in Addresses Table'/`address_id` int(11) DEFAULT NULL COMMENT 'Residential address'/g
s/`postaladdress` int(11) DEFAULT NULL COMMENT 'Pointer to address in Addresses Table'/`address_id_2` int(11) DEFAULT NULL COMMENT 'Postal address'/g
s/`preferredname`/`preferred_name`/g
s/`useemail`/`use_email`/g
s/`committeemember`/`committee_member`/g
s/`committeeposition`/`committee_position`/g
s/`paymentmethod`/`payment_method`/g
s/`columnwidths`/`column_widths`/g
s/`column_widths` int(11) DEFAULT NULL/`report_column_widths_id` int(11) DEFAULT NULL/g
s/`headertitle`/`header_title`/g
s/`header_title` int(11) DEFAULT NULL/`report_header_title_id` int(11) DEFAULT NULL/g
s/`pagename` int(11) DEFAULT NULL COMMENT 'If PageName/`report_page_name_id` int(11) DEFAULT NULL COMMENT 'If PageName/g
s/sql` int(11) DEFAULT NULL/report_sql_id` int(11) DEFAULT NULL/g
s/`sql4anotheryear` int(11) DEFAULT NULL/`report_sql_id_2` int(11) DEFAULT NULL COMMENT 'Pointer to the SQL that retrieves data from not the current year'/g
s/`pagename`/`name`/g
s/`pagetype`/`type`/g
s/`querystring`/`query_string`/g
s/`query_string` int(11) DEFAULT NULL/`report_query_string_id` int(11) DEFAULT NULL/g
s/`tabletolookup`/`table_to_lookup`/g
s/`table_to_lookup` int(11) DEFAULT NUL/`report_table_to_lookup_id` int(11) DEFAULT NUL/g
s/`fieldtitle`/`field_title`/g
s/`yearlyreset`/`yearly_reset`/g
s/`weeksinterm`/`weeks_in_term`/g
s/`headerimage`/`header_image`/g
s/`dbhome`/`db_home`/g
s/`dbhomelocal`/`db_home_local`/g
s/`numberofterms`/`number_of_terms`/g
s/`emailofdbadmin`/`email_of_dbadmin`/g
s/`first_name` int(11) NOT NULL/`first_name` int(11) NOT NULL COMMENT 'Column number which holds the first_name field'/g
s/`last_name` int(11) NOT NULL/`last_name` int(11) NOT NULL COMMENT 'Column number which holds the last_name field'/g
s/`email` int(11) NOT NULL/`email` int(11) NOT NULL COMMENT 'Column number which holds the email field'/g
s/ADD UNIQUE KEY `onceperyear` (`member`,`year`)/ADD UNIQUE KEY `onceperyear` (`person_id`,`year`)/g
s/`containsparameter` tinyint(1) DEFAULT '0'/`contains_parameter` tinyint(1) DEFAULT '0'/g
#
#convert all foreign keys
s/`coursesession`/`session_id`/g
s/`attendee`/`person_id`/g
s/`course`/`course_id`/g
s/`venue`/`venue_id`/g
s/`primaryfacilitator`/`person_id`/g
s/secondaryfacilitator/person_id_2/g
s/`user`/`person_id`/g
s/`nextmenu`/`menu_id`/g
s/`report`/`report_id`/g
s/`address`/`address_id`/g
s/`partner`/`person_id`/g
s/`emergencycontact` int(11) DEFAULT NULL/`person_id_2` int(11) DEFAULT NULL COMMENT 'Emergency contact; pointer to Persons Table'/g
s/`contact`/`person_id`/g
#
#remove specification of engine and character set
s/ ENGINE=InnoDB DEFAULT CHARSET=utf8//g
s/ ENGINE=MyISAM DEFAULT CHARSET=latin1//g
s/ ENGINE=InnoDB DEFAULT CHARSET=latin1//g
