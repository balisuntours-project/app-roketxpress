<?php
class ModelReconfirmation extends CI_Model {

	public function __construct(){
		parent::__construct(); 
		$this->load->database();
	}
		
	public function getDataReconfirmation($scheduleDate, $idSource, $sendingMethod, $bookingCode, $customerName, $reservationTitle, $viewUnreadThread, $statusReconfirm){
		
		$con_date				=	"A.RESERVATIONDATESTART = '".$scheduleDate."'";
		$con_source				=	!isset($idSource) || $idSource == "" ? "1=1" : "A.IDSOURCE = ".$idSource;
		$con_sendingMethod		=	!isset($sendingMethod) || $sendingMethod == "" ? "1=1" : "D.SENDINGMETHOD = ".$sendingMethod;
		$con_bookingCode		=	!isset($bookingCode) || $bookingCode == "" ? "1=1" : "A.BOOKINGCODE LIKE '%".$bookingCode."%'";
		$con_customerName		=	!isset($customerName) || $customerName == "" ? "1=1" : "A.CUSTOMERNAME LIKE '%".$customerName."%'";
		$con_reservationTitle	=	!isset($reservationTitle) || $reservationTitle == "" ? "1=1" : "A.RESERVATIONTITLE LIKE '%".$reservationTitle."%'";
		$con_statusReconfirm	=	!isset($statusReconfirm) || $statusReconfirm == "" ? "1=1" : "D.STATUS = ".$statusReconfirm;
		$condition				=	$viewUnreadThread ?
									"D.STATUSREADTHREAD = 0" :
									$con_date." AND ".$con_source." AND ".$con_sendingMethod." AND ".$con_bookingCode." AND ".$con_customerName." AND ".$con_reservationTitle." AND ".$con_statusReconfirm;
		$orderBy				=	$viewUnreadThread ? "D.DATETIMERESPONSE ASC" : "A.IDRESERVATION DESC";
		$baseQuery				=	"SELECT A.RESERVATIONTITLE, DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
											DATE_FORMAT(A.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, LEFT(A.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
											LEFT(A.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND, A.DURATIONOFDAY, A.BOOKINGCODE, A.INPUTTYPE, B.SOURCENAME, A.CUSTOMERNAME,
											A.CUSTOMERCONTACT, A.CUSTOMEREMAIL, IF(A.IDAREA = -1, 'Without Transfer', IFNULL(C.AREANAME, '-')) AS AREANAME, A.HOTELNAME,
											A.PICKUPLOCATION, A.DROPOFFLOCATION, A.REMARK, A.TOURPLAN, A.SPECIALREQUEST, CONCAT(
                                                '[',
                                                    GROUP_CONCAT(
														IF(
															D.PLATFORM IS NULL,
															'{}',
															JSON_OBJECT(
																'PLATFORM',
																D.PLATFORM,
																'CONTACT',
																D.CONTACT,
																'DATETIMESCHEDULE',
																IFNULL(DATE_FORMAT(D.DATETIMESCHEDULE, '%d %b %Y %H:%i'), '-'),
																'DATETIMESENT',
																IFNULL(DATE_FORMAT(D.DATETIMESENT, '%d %b %Y %H:%i'), '-'),
																'DATETIMERESPONSE',
																IFNULL(DATE_FORMAT(D.DATETIMERESPONSE, '%d %b %Y %H:%i'), '-'),
																'STATUS',
																D.STATUS,
																'SENDINGMETHOD',
																D.SENDINGMETHOD,
																'STATUSREADTHREAD',
																D.STATUSREADTHREAD
															)
														)
                                                        ORDER BY D.DATETIMESCHEDULE
                                                    ),
                                                ']'
                                            ) AS OBJRECONFIRMATION, A.IDRESERVATION
									FROM t_reservation A
									LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
									LEFT JOIN m_area C ON A.IDAREA = C.IDAREA
									LEFT JOIN t_reservationreconfirmation D ON A.IDRESERVATION = D.IDRESERVATION
									WHERE ".$condition." AND A.STATUS != -1
									GROUP BY A.IDRESERVATION
									ORDER BY ".$orderBy;
		$query					=	$this->db->query($baseQuery);
		$result					=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
		
	}
	
	public function getDetailStrReservation($idReservation){
		$query	= $this->db->query("SELECT A.IDCONTACT, A.RESERVATIONTITLE, DATE_FORMAT(A.RESERVATIONDATESTART, '%d %b %Y') AS RESERVATIONDATESTART,
											DATE_FORMAT(A.RESERVATIONDATEEND, '%d %b %Y') AS RESERVATIONDATEEND, LEFT(A.RESERVATIONTIMESTART, 5) AS RESERVATIONTIMESTART,
											LEFT(A.RESERVATIONTIMEEND, 5) AS RESERVATIONTIMEEND, A.DURATIONOFDAY, A.BOOKINGCODE, A.INPUTTYPE, B.SOURCENAME, A.CUSTOMERNAME,
											A.CUSTOMERCONTACT, A.CUSTOMEREMAIL, A.NUMBEROFADULT, A.NUMBEROFCHILD, A.NUMBEROFINFANT, A.ADDITIONALINFOLIST,
											IF(A.IDAREA = -1, 'Without Transfer', IFNULL(C.AREANAME, '-')) AS AREANAME, A.HOTELNAME, A.PICKUPLOCATION, A.DROPOFFLOCATION,
											A.REMARK, A.TOURPLAN, A.SPECIALREQUEST, A.URLDETAILPRODUCT, A.URLPICKUPLOCATION, CONCAT(
                                                '[',
                                                    GROUP_CONCAT(
														IF(
															D.PLATFORM IS NULL,
															'{}',
															JSON_OBJECT(
																'PLATFORM',
																D.PLATFORM,
																'CONTACT',
																D.CONTACT,
																'ADDITIONALINFOLIST',
																IFNULL(D.ADDITIONALINFOLIST, ''),
																'MAILHTMLFILE',
																D.MAILHTMLFILE,
																'MAILTHREADFILE',
																IFNULL(D.MAILTHREADFILE, ''),
																'DATETIMESCHEDULE',
																IFNULL(DATE_FORMAT(D.DATETIMESCHEDULE, '%d %b %Y %H:%i'), '-'),
																'DATETIMESENT',
																IFNULL(DATE_FORMAT(D.DATETIMESENT, '%d %b %Y %H:%i'), '-'),
																'DATETIMERESPONSE',
																IFNULL(DATE_FORMAT(D.DATETIMERESPONSE, '%d %b %Y %H:%i'), '-'),
																'STATUSREAD',
																D.STATUSREAD,
																'DATETIMEREAD',
																IFNULL(DATE_FORMAT(D.DATETIMEREAD, '%d %b %Y %H:%i'), '-'),
																'STATUS',
																D.STATUS,
																'IDRESERVATIONRECONFIRMATION',
																D.IDRESERVATIONRECONFIRMATION
															)
														)
                                                        ORDER BY D.DATETIMESCHEDULE
                                                    ),
                                                ']'
                                            ) AS OBJRECONFIRMATION
									FROM t_reservation A
									LEFT JOIN m_source B ON A.IDSOURCE = B.IDSOURCE
									LEFT JOIN m_area C ON A.IDAREA = C.IDAREA
									LEFT JOIN t_reservationreconfirmation D ON A.IDRESERVATION = D.IDRESERVATION
									WHERE A.IDRESERVATION = '".$idReservation."'
									LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;		
	}

	public function getDetailReconfirmation($idReconfirmationMail){
		$query	= $this->db->query("SELECT A.CONTACT, B.CUSTOMERNAME, A.MAILMESSAGEID, A.MAILTHREADARRNAME, A.MAILSUBJECT, A.MAILTHREADFILE,
										   B.BOOKINGCODE, A.ADDITIONALINFOLIST, A.IDRESERVATION
									FROM t_reservationreconfirmation A
									LEFT JOIN t_reservation B ON A.IDRESERVATION = B.IDRESERVATION
									WHERE A.IDRESERVATIONRECONFIRMATION = '".$idReconfirmationMail."'
									LIMIT 1");
		$row	= $query->row_array();

		if(isset($row)){
			return $row;
		}
		
		return false;		
	}
	
	public function getMailTemplateData($idUserAdmin){
		$baseQuery	=	"SELECT IDMAILMESSAGETEMPLATE, LABEL, STATUSSIGNATURE, CONTENT
						FROM t_mailmessagetemplate
						WHERE IDUSERADMIN = ".$idUserAdmin."
						ORDER BY STATUSSIGNATURE DESC, LABEL";
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(isset($result)){
			return $result;
		}
		
		return false;
	}	
	
	public function getReservationHandleDriver($idReservation){
		$baseQuery	=	sprintf(
							"SELECT CONCAT(D.DRIVERTYPE, ' Driver') AS PARTNERTYPE, C.NAME AS PARTNERNAME, DATE_FORMAT(A.SCHEDULEDATE, '%s') AS SCHEDULEDATE,
									IF(B.DRIVERPHONENUMBER IS NULL OR B.DRIVERPHONENUMBER = '', '-', B.DRIVERPHONENUMBER) AS DRIVERPHONENUMBER, B.CARBRANDMODEL, B.CARNUMBERPLATE
							 FROM t_reservationdetails A
							 LEFT JOIN t_scheduledriver B ON A.IDRESERVATIONDETAILS = B.IDRESERVATIONDETAILS
							 LEFT JOIN m_driver C ON B.IDDRIVER = C.IDDRIVER
							 LEFT JOIN m_drivertype D ON C.IDDRIVERTYPE = D.IDDRIVERTYPE
							 WHERE A.IDRESERVATION = ".$idReservation." AND B.IDSCHEDULEDRIVER IS NOT NULL AND A.STATUS = 1
							 ORDER BY D.DRIVERTYPE, C.NAME"
							, '%d %b %Y'
						);
		$query		=	$this->db->query($baseQuery);
		$result		=	$query->result();
		
		if(!$result) return array();
		return $result;	
	}
}