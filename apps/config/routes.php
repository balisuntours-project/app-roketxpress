<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller']																	= 'Main';
$route['404_override']																			= '';
$route['translate_uri_dashes']																	= FALSE;
		
$route['logout/(:any)']['GET']																	= 'Main/logout/$1';
$route['loginPage']['GET']																		= 'Main/loginPage';
$route['mainPage']['POST']																		= 'Main/mainPage';
$route['organization']['GET']																	= 'Main/organization';
$route['privacy-policy']['GET']																	= 'Main/privacyPolicy';
$route['accessCheck']['POST']																	= 'Access/accessCheck';
$route['userLogin']['POST']																		= 'Access/userLogin';
$route['unreadNotificationList']['POST']														= 'Access/unreadNotificationList';
$route['bulkNotifMailbox']['GET']																= 'Mailbox/bulkNotifMailbox';
$route['bulkNotifReservation']['GET']															= 'Mailbox/bulkNotifReservation';
$route['testAPINotifOrder']['GET']																= 'Schedule/DriverSchedule/testAPINotifOrder';

$route['option-helper/getDataOption/(:any)']['GET']												= 'OptionHelper/getDataOption/$1';
$route['assetsapi/mail/(:any)/(:any)']['GET']													= 'AssetsAPI/mail/$1/$2';
$route['assetsapi/mailReview/(:any)/(:any)']['GET']												= 'AssetsAPI/mailReview/$1/$2';
$route['assetsapi/mailReconfirmation/(:any)/(:any)']['GET']										= 'AssetsAPI/mailReconfirmation/$1/$2';
$route['assetsapi/mailReconfirmationAttachment/(:any)']['GET']									= 'AssetsAPI/mailReconfirmationAttachment/$1';

$route['settingUser/detailSetting']['POST']														= 'SettingUser/detailSetting';
$route['settingUser/saveSetting']['POST']														= 'SettingUser/saveSetting';
$route['settingUser/detailUserProfileSetting']['POST']											= 'SettingUser/detailUserProfileSetting';
$route['settingUser/insertMailTemplate']['POST']												= 'SettingUser/insertMailTemplate';
$route['settingUser/detailMailMessageTemplate']['POST']											= 'SettingUser/detailMailMessageTemplate';
$route['settingUser/updateMailTemplate']['POST']												= 'SettingUser/updateMailTemplate';
$route['settingUser/deleteMailTemplate']['POST']												= 'SettingUser/deleteMailTemplate';

$route['notification/getDataNotification']['POST']												= 'Notification/getDataNotification';
$route['notification/dismissNotification']['POST']												= 'Notification/dismissNotification';
$route['notification/dismissAllNotification']['POST']											= 'Notification/dismissAllNotification';

$route['view/notification']['POST']																= 'View/notification';
$route['view/userProfileSetting']['POST']														= 'View/userProfileSetting';
$route['view/data-master']['POST']																= 'View/dataMaster';
$route['view/self-drive-setting']['POST']														= 'View/selfDriveSetting';
$route['view/ticket-setting']['POST']															= 'View/ticketSetting';
$route['view/transport-setting']['POST']														= 'View/transportSetting';
$route['view/template-auto-cost']['POST']														= 'View/templateAutoCost';
$route['view/mailbox']['POST']																	= 'View/mailbox';
$route['view/reservation']['POST']																= 'View/reservation';
$route['view/import-data-ota']['POST']															= 'View/importDataOTA';
$route['view/re-confirmation']['POST']															= 'View/reConfirmation';
$route['view/schedule-driver']['POST']															= 'View/scheduleDriver';
$route['view/schedule-driver-rating-point']['POST']												= 'View/scheduleDriverRatingPoint';
$route['view/schedule-driver-auto']['POST']														= 'View/scheduleDriverAuto';
$route['view/schedule-driver-monitor']['POST']													= 'View/scheduleDriverMonitor';
$route['view/schedule-vendor']['POST']															= 'View/scheduleVendor';
$route['view/schedule-car']['POST']																= 'View/scheduleCar';
$route['view/finance-detail-reservation-payment']['POST']										= 'View/financeDetailReservationPayment';
$route['view/finance-detail-reservation-income']['POST']										= 'View/financeDetailReservationIncome';
$route['view/finance-reservation-invoice']['POST']												= 'View/financeReservationInvoice';
$route['view/finance-reimbursement']['POST']													= 'View/financeReimbursement';
$route['view/finance-recap-fee-product']['POST']												= 'View/financeRecapFeeProduct';
$route['view/finance-charity-report']['POST']													= 'View/financeCharityReport';
$route['view/finance-transfer-list']['POST']													= 'View/financeTransferList';
$route['view/finance-currency-exchange']['POST']												= 'View/financeCurrencyExchange';
$route['view/finance-driver-recap-per-driver']['POST']											= 'View/financeDriverRecapPerDriver';
$route['view/finance-driver-cost-fee']['POST']													= 'View/financeDriverCostFee';
$route['view/finance-driver-additional-cost']['POST']											= 'View/financeDriverAdditionalCost';
$route['view/finance-driver-collect-payment']['POST']											= 'View/financeDriverCollectPayment';
$route['view/finance-driver-loan-prepaid-capital']['POST']										= 'View/financeDriverLoanPrepaidCapital';
$route['view/finance-driver-review-bonus-punishment']['POST']									= 'View/financeDriverReviewBonusPunishment';
$route['view/finance-driver-additional-income']['POST']											= 'View/financeDriverAdditionalIncome';
$route['view/finance-vendor-recap-per-vendor']['POST']											= 'View/financeVendorRecapPerVendor';
$route['view/finance-vendor-detail-fee-vendor']['POST']											= 'View/financeVendorDetailFeeVendor';
$route['view/finance-vendor-collect-payment']['POST']											= 'View/financeVendorCollectPayment';
$route['view/finance-vendor-car-rental-fee-cost']['POST']										= 'View/financeVendorCarRentalFeeCost';
$route['view/report-agent-payment-balance']['POST']												= 'View/reportAgentPaymentBalance';
$route['view/report-reservation-details']['POST']												= 'View/reportReservationDetail';
$route['view/report-recap-per-product']['POST']													= 'View/reportRecapPerProduct';
$route['view/report-recap-per-date']['POST']													= 'View/reportRecapPerDate';
$route['view/user-level']['POST']																= 'View/settingUserLevel';
$route['view/user-level-menu']['POST']															= 'View/settingUserLevelMenu';
$route['view/admin-user']['POST']																= 'View/settingUserAdmin';
$route['view/system-settings']['POST']															= 'View/settingSystem';
$route['view/help-center']['POST']																= 'View/settingHelpCenter';
$route['view/partner-user-level']['POST']														= 'View/settingPartnerUserLevel';
$route['view/partner-user-level-menu']['POST']													= 'View/settingPartnerUserLevelMenu';
$route['view/partner-user']['POST']																= 'View/settingUserPartner';
$route['view/knowledge']['POST']																= 'View/knowledge';

$route['foto/sourceLogo/(:any)']['GET']															= 'Foto/sourceLogo/$1';
$route['foto/bankLogo/(:any)']['GET']															= 'Foto/bankLogo/$1';
$route['foto/transferReceipt/(:any)']['GET']													= 'Foto/transferReceipt/$1';
$route['foto/carCostReceipt/(:any)']['GET']														= 'Foto/carCostReceipt/$1';
$route['foto/reimbursement/(:any)']['GET']														= 'Foto/reimbursement/$1';

$route['file/reservationInvoice/(:any)']['GET']													= 'File/reservationInvoice/$1';
$route['file/reservationVoucher/(:any)']['GET']													= 'File/reservationVoucher/$1';
$route['file/xlsxTransferList/(:any)']['GET']													= 'File/xlsxTransferList/$1';
$route['file/transferReceiptHTML/(:any)']['GET']												= 'File/transferReceiptHTML/$1';

$route['cron/readKlikBCAPayroll']['GET']														= 'Cron/readKlikBCAPayroll';
$route['cron/readMailbox']['GET']																= 'Cron/readMailbox';
$route['cron/readMailboxBookingCodeParam/(:any)']['GET']										= 'Cron/readMailboxBookingCodeParam/$1';
$route['cron/readMailboxBookingCodeParamCS/(:any)']['GET']										= 'Cron/readMailboxBookingCodeParamCS/$1';
$route['cron/readMailboxTestRTDB']['GET']														= 'Cron/readMailboxTestRTDB';
$route['cron/autoRejectDayOffRequestYesterday']['GET']											= 'Cron/autoRejectDayOffRequestYesterday';
$route['cron/autoConfirmDriverLoanTransaction']['GET']											= 'Cron/autoConfirmDriverLoanTransaction';
$route['cron/calculateScheduleDriverMonitor']['GET']											= 'Cron/calculateScheduleDriverMonitor';
$route['cron/readMailboxCorrection']['GET']														= 'Cron/readMailboxCorrection';
$route['cron/createScheduleReservationConfirmation']['GET']										= 'Cron/createScheduleReservationConfirmation';
$route['cron/previewMailReconfirmation/(:any)']['GET']											= 'Cron/previewMailReconfirmation/$1';
$route['cron/sendMailReconfirmation/(:any)']['GET']												= 'Cron/sendMailReconfirmation/$1';
$route['cron/readMailReconfirmation']['GET']													= 'Cron/readMailReconfirmation';
$route['cron/createReviewBonusPeriodTarget']['GET']												= 'Cron/createReviewBonusPeriodTarget';
$route['cron/cronScanCustomerContact']['GET']													= 'Cron/cronScanCustomerContact';
$route['cron/apiScanCustomerContact/(:any)']['GET']												= 'Cron/apiScanCustomerContact/$1';
$route['cron/cronEBookingCoinNonDriver']['GET']													= 'Cron/cronEBookingCoinNonDriver';
$route['cron/calculateRatingPointAndRecapAdditionalIncomeDriver']['GET']						= 'Cron/calculateRatingPointAndRecapAdditionalIncomeDriver';
$route['cron/readKlookBadReviewMail']['GET']													= 'Cron/readKlookBadReviewMail';
$route['crontest/cronScanCustomerContact']['GET']												= 'CronTest/cronScanCustomerContact';

$route['dashboard/getDataDashboard']['POST']													= 'Dashboard/getDataDashboard';

$route['masterSource/getDataTable']['POST']														= 'Master/MasterSource/getDataTable';
$route['masterSource/insertData']['POST']														= 'Master/MasterSource/insertData';
$route['masterSource/detailData']['POST']														= 'Master/MasterSource/detailData';
$route['masterSource/updateData']['POST']														= 'Master/MasterSource/updateData';
$route['masterSource/deleteData']['POST']														= 'Master/MasterSource/deleteData';
$route['masterSource/uploadLogoSource/(:any)']['POST']											= 'Master/MasterSource/uploadLogoSource/$1';

$route['masterProduct/getDataTable']['POST']													= 'Master/MasterProduct/getDataTable';
$route['masterProduct/insertData']['POST']														= 'Master/MasterProduct/insertData';
$route['masterProduct/detailData']['POST']														= 'Master/MasterProduct/detailData';
$route['masterProduct/updateData']['POST']														= 'Master/MasterProduct/updateData';
$route['masterProduct/deleteData']['POST']														= 'Master/MasterProduct/deleteData';

$route['masterDriver/getDataTable']['POST']														= 'Master/MasterDriver/getDataTable';
$route['masterDriver/insertData']['POST']														= 'Master/MasterDriver/insertData';
$route['masterDriver/detailData']['POST']														= 'Master/MasterDriver/detailData';
$route['masterDriver/updateData']['POST']														= 'Master/MasterDriver/updateData';
$route['masterDriver/updateStatus']['POST']														= 'Master/MasterDriver/updateStatus';
$route['masterDriver/getDriverRank']['POST']													= 'Master/MasterDriver/getDriverRank';
$route['masterDriver/saveDriverRank']['POST']													= 'Master/MasterDriver/saveDriverRank';
$route['masterDriver/getDriverAreaOrder']['POST']												= 'Master/MasterDriver/getDriverAreaOrder';
$route['masterDriver/saveDriverAreaOrder']['POST']												= 'Master/MasterDriver/saveDriverAreaOrder';
$route['masterDriver/resetDriverSecretPin']['POST']												= 'Master/MasterDriver/resetDriverSecretPin';
$route['masterDriver/setPartnerNewFinanceScheme']['POST']										= 'Master/MasterDriver/setPartnerNewFinanceScheme';

$route['masterVendor/getDataTable']['POST']														= 'Master/MasterVendor/getDataTable';
$route['masterVendor/insertData']['POST']														= 'Master/MasterVendor/insertData';
$route['masterVendor/detailData']['POST']														= 'Master/MasterVendor/detailData';
$route['masterVendor/updateData']['POST']														= 'Master/MasterVendor/updateData';
$route['masterVendor/updateStatus']['POST']														= 'Master/MasterVendor/updateStatus';
$route['masterVendor/resetVendorSecretPin']['POST']												= 'Master/MasterVendor/resetVendorSecretPin';
$route['masterVendor/setPartnerNewFinanceScheme']['POST']										= 'Master/MasterVendor/setPartnerNewFinanceScheme';
$route['masterVendor/updateLastWithdrawVendor']['POST']											= 'Master/MasterVendor/updateLastWithdrawVendor';

$route['masterCarType/getDataTable']['POST']													= 'Master/MasterCarType/getDataTable';
$route['masterCarType/insertData']['POST']														= 'Master/MasterCarType/insertData';
$route['masterCarType/detailData']['POST']														= 'Master/MasterCarType/detailData';
$route['masterCarType/updateData']['POST']														= 'Master/MasterCarType/updateData';

$route['productSetting/selfDrive/getDataSelfDriveFees']['POST']									= 'ProductSetting/SelfDrive/getDataSelfDriveFees';
$route['productSetting/selfDrive/addSelfDriveFee']['POST']										= 'ProductSetting/SelfDrive/addSelfDriveFee';
$route['productSetting/selfDrive/detailSelfDriveFee']['POST']									= 'ProductSetting/SelfDrive/detailSelfDriveFee';
$route['productSetting/selfDrive/updateSelfDriveFee']['POST']									= 'ProductSetting/SelfDrive/updateSelfDriveFee';
$route['productSetting/selfDrive/deleteSelfDriveFee']['POST']									= 'ProductSetting/SelfDrive/deleteSelfDriveFee';

$route['productSetting/selfDrive/getDataVendorCar']['POST']										= 'ProductSetting/SelfDrive/getDataVendorCar';
$route['productSetting/selfDrive/addVendorCar']['POST']											= 'ProductSetting/SelfDrive/addVendorCar';
$route['productSetting/selfDrive/updateVendorCar']['POST']										= 'ProductSetting/SelfDrive/updateVendorCar';
$route['productSetting/selfDrive/detailVendorCar']['POST']										= 'ProductSetting/SelfDrive/detailVendorCar';
$route['productSetting/selfDrive/updateStatusVendorCar']['POST']								= 'ProductSetting/SelfDrive/updateStatusVendorCar';

$route['productSetting/ticket/getDataTicketVendorPrice']['POST']								= 'ProductSetting/Ticket/getDataTicketVendorPrice';
$route['productSetting/ticket/addTicketVendorPrice']['POST']									= 'ProductSetting/Ticket/addTicketVendorPrice';
$route['productSetting/ticket/detailTicketVendorPrice']['POST']									= 'ProductSetting/Ticket/detailTicketVendorPrice';
$route['productSetting/ticket/updateTicketVendorPrice']['POST']									= 'ProductSetting/Ticket/updateTicketVendorPrice';
$route['productSetting/ticket/deleteTicketVendorPrice']['POST']									= 'ProductSetting/Ticket/deleteTicketVendorPrice';

$route['productSetting/transport/getDataDriverFees']['POST']									= 'ProductSetting/Transport/getDataDriverFees';
$route['productSetting/transport/getDetailDriverFee']['POST']									= 'ProductSetting/Transport/getDetailDriverFee';
$route['productSetting/transport/getOptionTransportProduct']['POST']							= 'ProductSetting/Transport/getOptionTransportProduct';
$route['productSetting/transport/saveDriverFee']['POST']										= 'ProductSetting/Transport/saveDriverFee';
$route['productSetting/transport/deleteDriverFee']['POST']										= 'ProductSetting/Transport/deleteDriverFee';
$route['productSetting/transport/getTransportProductRank']['POST']								= 'ProductSetting/Transport/getTransportProductRank';
$route['productSetting/transport/saveTransportProductRank']['POST']								= 'ProductSetting/Transport/saveTransportProductRank';

$route['productSetting/templateAutoCost/getDataTemplateAutoCost']['POST']						= 'ProductSetting/TemplateAutoCost/getDataTemplateAutoCost';
$route['productSetting/templateAutoCost/insertTemplateAutoCost']['POST']						= 'ProductSetting/TemplateAutoCost/insertTemplateAutoCost';
$route['productSetting/templateAutoCost/getDataProductTicketTransport']['POST']					= 'ProductSetting/TemplateAutoCost/getDataProductTicketTransport';
$route['productSetting/templateAutoCost/insertTemplateAutoCostDetail']['POST']					= 'ProductSetting/TemplateAutoCost/insertTemplateAutoCostDetail';
$route['productSetting/templateAutoCost/updateTemplateAutoCostName']['POST']					= 'ProductSetting/TemplateAutoCost/updateTemplateAutoCostName';
$route['productSetting/templateAutoCost/insertTemplateAutoCostKeyword']['POST']					= 'ProductSetting/TemplateAutoCost/insertTemplateAutoCostKeyword';
$route['productSetting/templateAutoCost/deleteTemplateAutoCostItem']['POST']					= 'ProductSetting/TemplateAutoCost/deleteTemplateAutoCostItem';
$route['productSetting/templateAutoCost/deleteTemplateAutoCostKeyword']['POST']					= 'ProductSetting/TemplateAutoCost/deleteTemplateAutoCostKeyword';
$route['productSetting/templateAutoCost/deleteTemplateAutoCost']['POST']						= 'ProductSetting/TemplateAutoCost/deleteTemplateAutoCost';

$route['mailbox/getDataMailbox']['POST']														= 'Mailbox/getDataMailbox';
$route['mailbox/getDetailMailbox']['POST']														= 'Mailbox/getDetailMailbox';
$route['mailbox/getPreviewMail/(:any)']['GET']													= 'Mailbox/getPreviewMail/$1';
$route['mailbox/saveReservation']['POST']														= 'Mailbox/saveReservation';
$route['mailbox/getTotalUnreadMail']['POST']													= 'Mailbox/getTotalUnreadMail';

$route['reservation/getOptionHelperReservationProduct']['POST']									= 'Reservation/getOptionHelperReservationProduct';
$route['reservation/getDataReservation']['POST']												= 'Reservation/getDataReservation';
$route['reservation/getDetailReservation']['POST']												= 'Reservation/getDetailReservation';
$route['reservation/addReservation']['POST']													= 'Reservation/addReservation';
$route['reservation/updateReservation']['POST']													= 'Reservation/updateReservation';
$route['reservation/getDetailReservationDetails']['POST']										= 'Reservation/getDetailReservationDetails';
$route['reservation/saveReservationDetails']['POST']											= 'Reservation/saveReservationDetails';
$route['reservation/APISaveReservationDetails/(:any)']['GET']									= 'Reservation/APISaveReservationDetails/$1';
$route['reservation/getReservationDetailsTicket']['POST']										= 'Reservation/getReservationDetailsTicket';
$route['reservation/getReservationDetailsTransport']['POST']									= 'Reservation/getReservationDetailsTransport';
$route['reservation/updateReservationDetailsTicket']['POST']									= 'Reservation/updateReservationDetailsTicket';
$route['reservation/updateReservationDetailsTransport']['POST']									= 'Reservation/updateReservationDetailsTransport';
$route['reservation/addKeywordAutoCost']['POST']												= 'Reservation/addKeywordAutoCost';
$route['reservation/autoAddReservationDetails']['POST']											= 'Reservation/autoAddReservationDetails';
$route['reservation/getDetailPayment']['POST']													= 'Reservation/getDetailPayment';
$route['reservation/addReservationPayment']['POST']												= 'Reservation/addReservationPayment';
$route['reservation/updateReservationPayment']['POST']											= 'Reservation/updateReservationPayment';
$route['reservation/deleteReservationPayment']['POST']											= 'Reservation/deleteReservationPayment';
$route['reservation/searchListReservationForVoucher']['POST']									= 'Reservation/searchListReservationForVoucher';
$route['reservation/getReservationVoucherList']['POST']											= 'Reservation/getReservationVoucherList';
$route['reservation/saveReservationVoucher']['POST']											= 'Reservation/saveReservationVoucher';
$route['reservation/deleteReservationVoucher']['POST']											= 'Reservation/deleteReservationVoucher';
$route['reservation/APIDeactivateReservationDetails']['GET']									= 'Reservation/APIDeactivateReservationDetails';
$route['reservation/deactivateReservationDetails']['POST']										= 'Reservation/deactivateReservationDetails';
$route['reservation/cancelReservation']['POST']													= 'Reservation/cancelReservation';
$route['reservation/updateRefundTypeReservation']['POST']										= 'Reservation/updateRefundTypeReservation';
$route['reservation/excelDetail/(:any)/token']['GET']											= 'Reservation/excelDetail/$1';
$route['reservation/excelVendorBook/(:any)/token']['GET']										= 'Reservation/excelVendorBook/$1';
$route['reservation/dataDetails/(:any)']['GET']													= 'Reservation/dataDetails/$1';

$route['importDataOTA/uploadExcelReservationOTA']['POST']										= 'ImportDataOTA/uploadExcelReservationOTA';
$route['importDataOTA/scanExcelReservationOTA']['POST']											= 'ImportDataOTA/scanExcelReservationOTA';

$route['reconfirmation/getDataReconfirmation']['POST']											= 'Reconfirmation/getDataReconfirmation';
$route['reconfirmation/getDetailReconfirmation']['POST']										= 'Reconfirmation/getDetailReconfirmation';
$route['reconfirmation/getPreviewReconfirmationMail/(:any)']['GET']								= 'Reconfirmation/getPreviewReconfirmationMail/$1';
$route['reconfirmation/updateWebappStatisticTagsUnreadThreadReconfirmation']['GET']				= 'Reconfirmation/updateWebappStatisticTagsUnreadThreadReconfirmation';
$route['reconfirmation/getMailThreadDetails']['POST']											= 'Reconfirmation/getMailThreadDetails';
$route['reconfirmation/replyMailConfirmation']['POST']											= 'Reconfirmation/replyMailConfirmation';
$route['reconfirmation/addMailConfirmationAdditionalInfo']['POST']								= 'Reconfirmation/addMailConfirmationAdditionalInfo';
$route['reconfirmation/deleteAdditionalInformation']['POST']									= 'Reconfirmation/deleteAdditionalInformation';
$route['reconfirmation/setToManualReconfirmation']['POST']										= 'Reconfirmation/setToManualReconfirmation';
$route['reconfirmation/sendManualReconfirmation']['POST']										= 'Reconfirmation/sendManualReconfirmation';
$route['reconfirmation/sendMessageWhatsappSetToken']['POST']									= 'Reconfirmation/sendMessageWhatsappSetToken';

$route['schedule/driverSchedule/getDataDriverSchedule']['POST']									= 'Schedule/DriverSchedule/getDataDriverSchedule';
$route['schedule/driverSchedule/getDataReservationSchedule']['POST']							= 'Schedule/DriverSchedule/getDataReservationSchedule';
$route['schedule/driverSchedule/getDataDriverCalendar']['POST']									= 'Schedule/DriverSchedule/getDataDriverCalendar';
$route['schedule/driverSchedule/getDataDayOffRequest']['POST']									= 'Schedule/DriverSchedule/getDataDayOffRequest';
$route['schedule/driverSchedule/approveDayOffRequest']['POST']									= 'Schedule/DriverSchedule/approveDayOffRequest';
$route['schedule/driverSchedule/rejectDayOffRequest']['POST']									= 'Schedule/DriverSchedule/rejectDayOffRequest';
$route['schedule/driverSchedule/deleteDayOff']['POST']											= 'Schedule/DriverSchedule/deleteDayOff';
$route['schedule/driverSchedule/getDataReservationList']['POST']								= 'Schedule/DriverSchedule/getDataReservationList';
$route['schedule/driverSchedule/getDataDriverList']['POST']										= 'Schedule/DriverSchedule/getDataDriverList';
$route['schedule/driverSchedule/saveDriverSchedule']['POST']									= 'Schedule/DriverSchedule/saveDriverSchedule';
$route['schedule/driverSchedule/saveReservationDetailsFee']['POST']								= 'Schedule/DriverSchedule/saveReservationDetailsFee';
$route['schedule/driverSchedule/deleteDriverSchedule']['POST']									= 'Schedule/DriverSchedule/deleteDriverSchedule';
$route['schedule/driverSchedule/getDetailReservation']['POST']									= 'Schedule/DriverSchedule/getDetailReservation';
$route['schedule/driverSchedule/getDetailDayOff']['POST']										= 'Schedule/DriverSchedule/getDetailDayOff';
$route['schedule/driverSchedule/resendScheduleNotification']['POST']							= 'Schedule/DriverSchedule/resendScheduleNotification';
$route['schedule/driverSchedule/saveDriverDayOff']['POST']										= 'Schedule/DriverSchedule/saveDriverDayOff';

$route['schedule/driverRatingPoint/getDataDriverRatingPoint']['POST']							= 'Schedule/DriverRatingPoint/getDataDriverRatingPoint';
$route['schedule/driverRatingPoint/getDataDriverRatingByDate']['POST']							= 'Schedule/DriverRatingPoint/getDataDriverRatingByDate';
$route['schedule/driverRatingPoint/saveDataDriverRatingPoint']['POST']							= 'Schedule/DriverRatingPoint/saveDataDriverRatingPoint';
$route['schedule/driverRatingPoint/deleteDriverRatingPoint']['POST']							= 'Schedule/DriverRatingPoint/deleteDriverRatingPoint';
$route['schedule/driverRatingPoint/saveDataSettingRatingPoint']['POST']							= 'Schedule/DriverRatingPoint/saveDataSettingRatingPoint';
$route['schedule/driverRatingPoint/getDataHistoryRatingPoint']['POST']							= 'Schedule/DriverRatingPoint/getDataHistoryRatingPoint';
$route['schedule/driverRatingPoint/scanInputRatingPointAuto']['POST']							= 'Schedule/DriverRatingPoint/scanInputRatingPointAuto';
$route['schedule/driverRatingPoint/saveInputRatingPointAuto']['POST']							= 'Schedule/DriverRatingPoint/saveInputRatingPointAuto';
$route['schedule/driverRatingPoint/saveDriverBasicPoint']['POST']								= 'Schedule/DriverRatingPoint/saveDriverBasicPoint';
$route['schedule/driverRatingPoint/refreshDriverPoint']['POST']									= 'Schedule/DriverRatingPoint/refreshDriverPoint';
$route['schedule/driverRatingPoint/getDataRatingCalendar']['POST']								= 'Schedule/DriverRatingPoint/getDataRatingCalendar';
$route['schedule/driverRatingPoint/getDetailReviewContent']['POST']								= 'Schedule/DriverRatingPoint/getDetailReviewContent';
$route['schedule/driverRatingPoint/apiSetPointRankDriver']['GET']								= 'Schedule/DriverRatingPoint/apiSetPointRankDriver';
$route['schedule/driverRatingPoint/apiCalculateBonusPunishmentReview/(:any)']['GET']			= 'Schedule/DriverRatingPoint/apiCalculateBonusPunishmentReview/$1';
$route['schedule/driverRatingPoint/fixDataReviewBonus/(:any)']['GET']							= 'Schedule/DriverRatingPoint/fixDataReviewBonus/$1';

$route['schedule/driverScheduleAuto/getDataAutoScheduleSetting']['POST']						= 'Schedule/DriverScheduleAuto/getDataAutoScheduleSetting';
$route['schedule/driverScheduleAuto/getDataScheduleAuto']['POST']								= 'Schedule/DriverScheduleAuto/getDataScheduleAuto';
$route['schedule/driverScheduleAuto/getDataDriverList']['POST']									= 'Schedule/DriverScheduleAuto/getDataDriverList';
$route['schedule/driverScheduleAuto/getDataScheduleManual']['POST']								= 'Schedule/DriverScheduleAuto/getDataScheduleManual';
$route['schedule/driverScheduleAuto/moveScheduleToManual']['POST']								= 'Schedule/DriverScheduleAuto/moveScheduleToManual';
$route['schedule/driverScheduleAuto/moveScheduleToAutomatic']['POST']							= 'Schedule/DriverScheduleAuto/moveScheduleToAutomatic';
$route['schedule/driverScheduleAuto/saveAutoSchedule']['POST']									= 'Schedule/DriverScheduleAuto/saveAutoSchedule';
$route['schedule/driverScheduleAuto/getDataDriverListManual']['POST']							= 'Schedule/DriverScheduleAuto/getDataDriverListManual';
$route['schedule/driverScheduleAuto/getDataDriverJobHistoryList']['POST']						= 'Schedule/DriverScheduleAuto/getDataDriverJobHistoryList';
$route['schedule/driverScheduleAuto/testPromise']['POST']										= 'Schedule/DriverScheduleAuto/testPromise';

$route['schedule/scheduleDriverMonitor/getDataScheduleDriverMonitor']['POST']					= 'Schedule/ScheduleDriverMonitor/getDataScheduleDriverMonitor';
$route['schedule/scheduleDriverMonitor/setDayOffQuotaPerDate']['POST']							= 'Schedule/ScheduleDriverMonitor/setDayOffQuotaPerDate';

$route['schedule/vendorSchedule/getDataVendorSchedule']['POST']									= 'Schedule/VendorSchedule/getDataVendorSchedule';
$route['schedule/vendorSchedule/getDetailReservation']['POST']									= 'Schedule/VendorSchedule/getDetailReservation';
$route['schedule/vendorSchedule/getDetailReservationDetailsProduct']['POST']					= 'Schedule/VendorSchedule/getDetailReservationDetailsProduct';
$route['schedule/vendorSchedule/updateSlotTimeBooking']['POST']									= 'Schedule/VendorSchedule/updateSlotTimeBooking';
$route['schedule/vendorSchedule/saveReservationDetails']['POST']								= 'Schedule/VendorSchedule/saveReservationDetails';
$route['schedule/vendorSchedule/resendScheduleNotification']['POST']							= 'Schedule/VendorSchedule/resendScheduleNotification';

$route['schedule/carSchedule/getDataCarSchedule']['POST']										= 'Schedule/CarSchedule/getDataCarSchedule';
$route['schedule/carSchedule/getDataReservationSchedule']['POST']								= 'Schedule/CarSchedule/getDataReservationSchedule';
$route['schedule/carSchedule/getDataUnScheduleCar']['POST']										= 'Schedule/CarSchedule/getDataUnScheduleCar';
$route['schedule/carSchedule/getDataCarList']['POST']											= 'Schedule/CarSchedule/getDataCarList';
$route['schedule/carSchedule/saveCarSchedule']['POST']											= 'Schedule/CarSchedule/saveCarSchedule';
$route['schedule/carSchedule/addCarDayOff']['POST']												= 'Schedule/CarSchedule/addCarDayOff';
$route['schedule/carSchedule/APISaveCarSchedule/(:any)']['GET']									= 'Schedule/CarSchedule/APISaveCarSchedule/$1';
$route['schedule/carSchedule/getDetailSchedule']['POST']										= 'Schedule/CarSchedule/getDetailSchedule';
$route['schedule/carSchedule/getDetailDayOff']['POST']											= 'Schedule/CarSchedule/getDetailDayOff';
$route['schedule/carSchedule/APIDeleteCarSchedule/(:any)']['GET']								= 'Schedule/CarSchedule/APIDeleteCarSchedule/$1';
$route['schedule/carSchedule/deleteCarSchedule']['POST']										= 'Schedule/CarSchedule/deleteCarSchedule';
$route['schedule/carSchedule/deleteCarDayOff']['POST']											= 'Schedule/CarSchedule/deleteCarDayOff';
$route['schedule/carSchedule/getDataDropOffPickUpSchedule']['POST']								= 'Schedule/CarSchedule/getDataDropOffPickUpSchedule';
$route['schedule/carSchedule/getDetailDropOffPickUpSchedule']['POST']							= 'Schedule/CarSchedule/getDetailDropOffPickUpSchedule';
$route['schedule/carSchedule/saveCarDropOffPickUpSchedule']['POST']							    = 'Schedule/CarSchedule/saveCarDropOffPickUpSchedule';
$route['schedule/carSchedule/getDataScheduleAdditionalCost']['POST']                            = 'Schedule/CarSchedule/getDataScheduleAdditionalCost';

$route['finance/detailReservationPayment/getDataReservationPayment']['POST']					= 'Finance/DetailReservationPayment/getDataReservationPayment';
$route['finance/detailReservationPayment/updateRevenueReservation']['POST']						= 'Finance/DetailReservationPayment/updateRevenueReservation';
$route['finance/detailReservationPayment/addReservationPayment']['POST']						= 'Finance/DetailReservationPayment/addReservationPayment';
$route['finance/detailReservationPayment/updateReservationPayment']['POST']						= 'Finance/DetailReservationPayment/updateReservationPayment';
$route['finance/detailReservationPayment/deleteReservationPayment']['POST']						= 'Finance/DetailReservationPayment/deleteReservationPayment';
$route['finance/detailReservationPayment/searchReservationByKeyword']['POST']					= 'Finance/DetailReservationPayment/searchReservationByKeyword';
$route['finance/detailReservationPayment/saveTransferDepositPayment']['POST']					= 'Finance/DetailReservationPayment/saveTransferDepositPayment';
$route['finance/detailReservationPayment/uploadExcelPaymentOTA']['POST']						= 'Finance/DetailReservationPayment/uploadExcelPaymentOTA';
$route['finance/detailReservationPayment/scanExcelPaymentOTA']['POST']							= 'Finance/DetailReservationPayment/scanExcelPaymentOTA';
$route['finance/detailReservationPayment/excelReport/(:any)/token']['GET']						= 'Finance/DetailReservationPayment/excelReport/$1';

$route['finance/detailReservationIncome/getDataDetailReservationIncome']['POST']				= 'Finance/DetailReservationIncome/getDataDetailReservationIncome';
$route['finance/detailReservationIncome/getDataRecapReservationIncome']['POST']					= 'Finance/DetailReservationIncome/getDataRecapReservationIncome';
$route['finance/detailReservationIncome/getDataRecapPerYear']['POST']							= 'Finance/DetailReservationIncome/getDataRecapPerYear';
$route['finance/detailReservationIncome/excelDetail/(:any)/token']['GET']						= 'Finance/DetailReservationIncome/excelDetail/$1';
$route['finance/detailReservationIncome/excelRecap/(:any)/token']['GET']						= 'Finance/DetailReservationIncome/excelRecap/$1';
$route['finance/detailReservationIncome/excelRecapPerYear/(:any)/token']['GET']					= 'Finance/DetailReservationIncome/excelRecapPerYear/$1';

$route['finance/reservationInvoice/searchDataReservation']['POST']								= 'Finance/ReservationInvoice/searchDataReservation';
$route['finance/reservationInvoice/getInvoiceNumberDetailCost']['POST']							= 'Finance/ReservationInvoice/getInvoiceNumberDetailCost';
$route['finance/reservationInvoice/submitReservationInvoice']['POST']							= 'Finance/ReservationInvoice/submitReservationInvoice';
$route['finance/reservationInvoice/getDataInvoiceHistory']['POST']								= 'Finance/ReservationInvoice/getDataInvoiceHistory';
$route['finance/reservationInvoice/viewMailContentDevel']['GET']								= 'Finance/ReservationInvoice/viewMailContentDevel';

$route['finance/reimbursement/getDataReimbursement']['POST']									= 'Finance/Reimbursement/getDataReimbursement';
$route['finance/reimbursement/getDetailReimbursement']['POST']									= 'Finance/Reimbursement/getDetailReimbursement';
$route['finance/reimbursement/submitValidateReimbursement']['POST']								= 'Finance/Reimbursement/submitValidateReimbursement';
$route['finance/reimbursement/uploadReimbursementReceipt']['POST']								= 'Finance/Reimbursement/uploadReimbursementReceipt';
$route['finance/reimbursement/insertUpdateReimbursement']['POST']								= 'Finance/Reimbursement/insertUpdateReimbursement';
$route['finance/reimbursement/cancelReimbursement']['POST']										= 'Finance/Reimbursement/cancelReimbursement';
$route['finance/reimbursement/excelDetail/(:any)/token']['GET']									= 'Finance/Reimbursement/excelDetail/$1';

$route['finance/recapFeePerProduct/getDataRecapFeePerProduct']['POST']							= 'Finance/RecapFeePerProduct/getDataRecapFeePerProduct';
$route['finance/recapFeePerProduct/excelRecapFeePerProduct/(:any)/token']['GET']				= 'Finance/RecapFeePerProduct/excelRecapFeePerProduct/$1';

$route['finance/charityReport/getDataCharityReport']['POST']									= 'Finance/CharityReport/getDataCharityReport';
$route['finance/charityReport/processDisburseCharity']['POST']									= 'Finance/CharityReport/processDisburseCharity';
$route['finance/charityReport/cancelCharityTransferProcess']['POST']							= 'Finance/CharityReport/cancelCharityTransferProcess';
$route['finance/charityReport/getDataCharityProcessTransfer']['POST']							= 'Finance/CharityReport/getDataCharityProcessTransfer';
$route['finance/charityReport/excelDataCharityReport/(:any)/token']['GET']						= 'Finance/CharityReport/excelDataCharityReport/$1';
$route['finance/charityReport/addDataManualCharity']['POST']									= 'Finance/CharityReport/addDataManualCharity';
$route['finance/charityReport/getDetailManualCharity']['POST']									= 'Finance/CharityReport/getDetailManualCharity';
$route['finance/charityReport/updateDataManualCharity']['POST']									= 'Finance/CharityReport/updateDataManualCharity';
$route['finance/charityReport/deleteDataManualCharity']['POST']									= 'Finance/CharityReport/deleteDataManualCharity';

$route['finance/transferList/getDataUnprocessed']['POST']										= 'Finance/TransferList/getDataUnprocessed';
$route['finance/transferList/cancelTransferList']['POST']										= 'Finance/TransferList/cancelTransferList';
$route['finance/transferList/createExcelPayrollTransferList']['POST']							= 'Finance/TransferList/createExcelPayrollTransferList';
$route['finance/transferList/getDataOngoing']['POST']											= 'Finance/TransferList/getDataOngoing';
$route['finance/transferList/uploadTransferReceipt/(:any)']['POST']								= 'Finance/TransferList/uploadTransferReceipt/$1';
$route['finance/transferList/saveManualTransfer']['POST']										= 'Finance/TransferList/saveManualTransfer';
$route['finance/transferList/getDataFinished']['POST']											= 'Finance/TransferList/getDataFinished';

$route['finance/currencyExchange/getDataCurrencyExchange']['POST']								= 'Finance/CurrencyExchange/getDataCurrencyExchange';
$route['finance/currencyExchange/addDataCurrencyExchange']['POST']								= 'Finance/CurrencyExchange/addDataCurrencyExchange';
$route['finance/currencyExchange/updateCurrencyExchange']['POST']								= 'Finance/CurrencyExchange/updateCurrencyExchange';
$route['finance/currencyExchange/deleteCurrencyExchange']['POST']								= 'Finance/CurrencyExchange/deleteCurrencyExchange';

$route['financeDriver/recapPerDriver/getDataAllDriverRecap']['POST']							= 'FinanceDriver/RecapPerDriver/getDataAllDriverRecap';
$route['financeDriver/recapPerDriver/getDataFeePerPeriod']['POST']								= 'FinanceDriver/RecapPerDriver/getDataFeePerPeriod';
$route['financeDriver/recapPerDriver/getDataPerDriverRecap']['POST']							= 'FinanceDriver/RecapPerDriver/getDataPerDriverRecap';
$route['financeDriver/recapPerDriver/getDataWithdrawalRequest']['POST']							= 'FinanceDriver/RecapPerDriver/getDataWithdrawalRequest';
$route['financeDriver/recapPerDriver/getDetailWithdrawalRequest']['POST']						= 'FinanceDriver/RecapPerDriver/getDetailWithdrawalRequest';
$route['financeDriver/recapPerDriver/submitManualWithdrawal']['POST']							= 'FinanceDriver/RecapPerDriver/submitManualWithdrawal';
$route['financeDriver/recapPerDriver/approveRejectWithdrawal']['POST']							= 'FinanceDriver/RecapPerDriver/approveRejectWithdrawal';
$route['financeDriver/recapPerDriver/cancelWithdrawal']['POST']									= 'FinanceDriver/RecapPerDriver/cancelWithdrawal';
$route['financeDriver/recapPerDriver/calculateWithdrawalRequest']['GET']						= 'FinanceDriver/RecapPerDriver/calculateWithdrawalRequest';
$route['financeDriver/recapPerDriver/excelAllDriverRecap/(:any)/token']['GET']					= 'FinanceDriver/RecapPerDriver/excelAllDriverRecap/$1';
$route['financeDriver/recapPerDriver/excelDataFeePerPeriod/(:any)/token']['GET']				= 'FinanceDriver/RecapPerDriver/excelDataFeePerPeriod/$1';

$route['financeDriver/detailCostFee/getDataDetailCostFee']['POST']								= 'FinanceDriver/DetailCostFee/getDataDetailCostFee';
$route['financeDriver/detailCostFee/excelDetailCostFee/(:any)/token']['GET']					= 'FinanceDriver/DetailCostFee/excelDetailCostFee/$1';

$route['financeDriver/additionalCost/getDataAdditionalCostApproval']['POST']					= 'FinanceDriver/AdditionalCost/getDataAdditionalCostApproval';
$route['financeDriver/additionalCost/uploadTransferReceipt/(:any)']['POST']						= 'FinanceDriver/AdditionalCost/uploadTransferReceipt/$1';
$route['financeDriver/additionalCost/submitValidateAdditionalCost']['POST']						= 'FinanceDriver/AdditionalCost/submitValidateAdditionalCost';
$route['financeDriver/additionalCost/calculateAdditionalCostRequest']['GET']					= 'FinanceDriver/AdditionalCost/calculateAdditionalCostRequest';
$route['financeDriver/additionalCost/getDataAdditionalCostHistory']['POST']						= 'FinanceDriver/AdditionalCost/getDataAdditionalCostHistory';
$route['financeDriver/additionalCost/searchListReservationForAdditionalCost']['POST']			= 'FinanceDriver/AdditionalCost/searchListReservationForAdditionalCost';
$route['financeDriver/additionalCost/saveNewAdditionalCost']['POST']							= 'FinanceDriver/AdditionalCost/saveNewAdditionalCost';

$route['financeDriver/collectPayment/getDataCollectPayment']['POST']							= 'FinanceDriver/CollectPayment/getDataCollectPayment';
$route['financeDriver/collectPayment/getDetailCollectPayment']['POST']							= 'FinanceDriver/CollectPayment/getDetailCollectPayment';
$route['financeDriver/collectPayment/uploadSettlementReceipt/(:any)/(:any)']['POST']			= 'FinanceDriver/CollectPayment/uploadSettlementReceipt/$1/$2';
$route['financeDriver/collectPayment/approveRejectCollectPaymentSettlement']['POST']			= 'FinanceDriver/CollectPayment/approveRejectCollectPaymentSettlement';
$route['financeDriver/collectPayment/calculateSettlementRequest']['GET']						= 'FinanceDriver/CollectPayment/calculateSettlementRequest';
$route['financeDriver/collectPayment/excelCollectPayment/(:any)/token']['GET']					= 'FinanceDriver/CollectPayment/excelCollectPayment/$1';

$route['financeDriver/loanPrepaidCapital/getDataLoanPrepaidCapital']['POST']					= 'FinanceDriver/LoanPrepaidCapital/getDataLoanPrepaidCapital';
$route['financeDriver/loanPrepaidCapital/excelLoanRecap/(:any)/token']['GET']					= 'FinanceDriver/LoanPrepaidCapital/excelLoanRecap/$1';
$route['financeDriver/loanPrepaidCapital/getListBankAccountDriver']['POST']						= 'FinanceDriver/LoanPrepaidCapital/getListBankAccountDriver';
$route['financeDriver/loanPrepaidCapital/saveNewLoanRecord']['POST']							= 'FinanceDriver/LoanPrepaidCapital/saveNewLoanRecord';
$route['financeDriver/loanPrepaidCapital/getDetailLoanPrepaidCapitalRequest']['POST']			= 'FinanceDriver/LoanPrepaidCapital/getDetailLoanPrepaidCapitalRequest';
$route['financeDriver/loanPrepaidCapital/approveRejectLoanPrepaidCapitalRequest']['POST']		= 'FinanceDriver/LoanPrepaidCapital/approveRejectLoanPrepaidCapitalRequest';
$route['financeDriver/loanPrepaidCapital/updateWebappStatisticTags']['GET']						= 'FinanceDriver/LoanPrepaidCapital/updateWebappStatisticTags';
$route['financeDriver/loanPrepaidCapital/getDetailHistoryLoanPrepaidCapital']['POST']			= 'FinanceDriver/LoanPrepaidCapital/getDetailHistoryLoanPrepaidCapital';
$route['financeDriver/loanPrepaidCapital/getDataLoanPerDriver']['POST']							= 'FinanceDriver/LoanPrepaidCapital/getDataLoanPerDriver';
$route['financeDriver/loanPrepaidCapital/uploadTransferReceiptInstallment/(:any)']['POST']		= 'FinanceDriver/LoanPrepaidCapital/uploadTransferReceiptInstallment/$1';
$route['financeDriver/loanPrepaidCapital/saveNewInstallmentRecord']['POST']						= 'FinanceDriver/LoanPrepaidCapital/saveNewInstallmentRecord';
$route['financeDriver/loanPrepaidCapital/excelLoanPerDriver/(:any)/token']['GET']				= 'FinanceDriver/LoanPrepaidCapital/excelLoanPerDriver/$1';
$route['financeDriver/loanPrepaidCapital/getDataLoanInstallmentRequest']['POST']				= 'FinanceDriver/LoanPrepaidCapital/getDataLoanInstallmentRequest';
$route['financeDriver/loanPrepaidCapital/getDetailLoanInstallmentRequest']['POST']				= 'FinanceDriver/LoanPrepaidCapital/getDetailLoanInstallmentRequest';
$route['financeDriver/loanPrepaidCapital/approveRejectInstallmentRequest']['POST']				= 'FinanceDriver/LoanPrepaidCapital/approveRejectInstallmentRequest';

$route['financeDriver/reviewBonusPunishment/getDataAllDriverReport']['POST']					= 'FinanceDriver/ReviewBonusPunishment/getDataAllDriverReport';
$route['financeDriver/reviewBonusPunishment/excelDataAllDriverReport/(:any)/token']['GET']		= 'FinanceDriver/ReviewBonusPunishment/excelDataAllDriverReport/$1';
$route['financeDriver/reviewBonusPunishment/getDataPeriodTargetRate']['POST']					= 'FinanceDriver/ReviewBonusPunishment/getDataPeriodTargetRate';
$route['financeDriver/reviewBonusPunishment/savePeriodTargetRate']['POST']						= 'FinanceDriver/ReviewBonusPunishment/savePeriodTargetRate';
$route['financeDriver/reviewBonusPunishment/updateTargetReviewPointDriver']['POST']				= 'FinanceDriver/ReviewBonusPunishment/updateTargetReviewPointDriver';

$route['financeDriver/additionalIncome/getDataAdditionalIncomeRecap']['POST']					= 'FinanceDriver/AdditionalIncome/getDataAdditionalIncomeRecap';
$route['financeDriver/additionalIncome/getDataAdditionalIncomeAndPointRateSetting']['POST']		= 'FinanceDriver/AdditionalIncome/getDataAdditionalIncomeAndPointRateSetting';
$route['financeDriver/additionalIncome/uploadTransferReceipt/(:any)']['POST']					= 'FinanceDriver/AdditionalIncome/uploadTransferReceipt/$1';
$route['financeDriver/additionalIncome/insertUpdateAdditionalIncome']['POST']					= 'FinanceDriver/AdditionalIncome/insertUpdateAdditionalIncome';
$route['financeDriver/additionalIncome/submitApprovalAdditionalIncome']['POST']					= 'FinanceDriver/AdditionalIncome/submitApprovalAdditionalIncome';
$route['financeDriver/additionalIncome/deleteAdditionalIncome']['POST']							= 'FinanceDriver/AdditionalIncome/deleteAdditionalIncome';
$route['financeDriver/additionalIncome/insertUpdatePointRate']['POST']							= 'FinanceDriver/AdditionalIncome/insertUpdatePointRate';
$route['financeDriver/additionalIncome/deleteAdditionalIncomeSettingPointRate']['POST']			= 'FinanceDriver/AdditionalIncome/deleteAdditionalIncomeSettingPointRate';
$route['financeDriver/additionalIncome/apiCalculateRatingPointDriver/(:any)']['GET']			= 'FinanceDriver/AdditionalIncome/apiCalculateRatingPointDriver/$1';
$route['financeDriver/additionalIncome/excelDetailAdditionalIncome/(:any)/token']['GET']		= 'FinanceDriver/AdditionalIncome/excelDetailAdditionalIncome/$1';

$route['financeVendor/recapPerVendor/getDataAllVendorReport']['POST']							= 'FinanceVendor/RecapPerVendor/getDataAllVendorReport';
$route['financeVendor/recapPerVendor/excelAllVendorReport/(:any)/token']['GET']					= 'FinanceVendor/RecapPerVendor/excelAllVendorReport/$1';
$route['financeVendor/recapPerVendor/getDataRecapPerVendor']['POST']							= 'FinanceVendor/RecapPerVendor/getDataRecapPerVendor';
$route['financeVendor/recapPerVendor/excelRecapPerVendor/(:any)/token']['GET']					= 'FinanceVendor/RecapPerVendor/excelRecapPerVendor/$1';
$route['financeVendor/recapPerVendor/getDataPerVendorRecap']['POST']							= 'FinanceVendor/RecapPerVendor/getDataPerVendorRecap';
$route['financeVendor/recapPerVendor/getDetailManualWithdraw']['POST']							= 'FinanceVendor/RecapPerVendor/getDetailManualWithdraw';
$route['financeVendor/recapPerVendor/saveNewBankAccountVendor']['POST']							= 'FinanceVendor/RecapPerVendor/saveNewBankAccountVendor';
$route['financeVendor/recapPerVendor/uploadDocumentInvoiceManualWithdraw/(:any)']['POST']		= 'FinanceVendor/RecapPerVendor/uploadDocumentInvoiceManualWithdraw/$1';
$route['financeVendor/recapPerVendor/uploadExcelInvoice/(:any)']['POST']						= 'FinanceVendor/RecapPerVendor/uploadExcelInvoice/$1';
$route['financeVendor/recapPerVendor/readExcelInvoiceVendor']['POST']							= 'FinanceVendor/RecapPerVendor/readExcelInvoiceVendor';
$route['financeVendor/recapPerVendor/saveManualWithdrawVendor']['POST']							= 'FinanceVendor/RecapPerVendor/saveManualWithdrawVendor';
$route['financeVendor/recapPerVendor/getDataListDepositHistory']['POST']						= 'FinanceVendor/RecapPerVendor/getDataListDepositHistory';
$route['financeVendor/recapPerVendor/uploadTransferReceiptDeposit/(:any)']['POST']				= 'FinanceVendor/RecapPerVendor/uploadTransferReceiptDeposit/$1';
$route['financeVendor/recapPerVendor/saveNewDepositRecord']['POST']								= 'FinanceVendor/RecapPerVendor/saveNewDepositRecord';
$route['financeVendor/recapPerVendor/getDataWithdrawalRequest']['POST']							= 'FinanceVendor/RecapPerVendor/getDataWithdrawalRequest';
$route['financeVendor/recapPerVendor/getDetailWithdrawalRequest']['POST']						= 'FinanceVendor/RecapPerVendor/getDetailWithdrawalRequest';
$route['financeVendor/recapPerVendor/approveRejectWithdrawal']['POST']							= 'FinanceVendor/RecapPerVendor/approveRejectWithdrawal';

$route['financeVendor/detailFeeVendor/getDetailFeeVendorCar']['POST']							= 'FinanceVendor/DetailFeeVendor/getDetailFeeVendorCar';
$route['financeVendor/detailFeeVendor/getDetailFeeVendorTicket']['POST']						= 'FinanceVendor/DetailFeeVendor/getDetailFeeVendorTicket';
$route['financeVendor/detailFeeVendor/excelDetailFeeVendorTicket/(:any)/token']['GET']			= 'FinanceVendor/DetailFeeVendor/excelDetailFeeVendorTicket/$1';

$route['financeVendor/collectPayment/getDataCollectPayment']['POST']							= 'FinanceVendor/CollectPayment/getDataCollectPayment';
$route['financeVendor/collectPayment/getDetailCollectPayment']['POST']							= 'FinanceVendor/CollectPayment/getDetailCollectPayment';
$route['financeVendor/collectPayment/uploadSettlementReceipt/(:any)/(:any)']['POST']			= 'FinanceVendor/CollectPayment/uploadSettlementReceipt/$1/$2';
$route['financeVendor/collectPayment/approveRejectCollectPaymentSettlement']['POST']			= 'FinanceVendor/CollectPayment/approveRejectCollectPaymentSettlement';
$route['financeVendor/collectPayment/excelCollectPayment/(:any)/token']['GET']					= 'FinanceVendor/CollectPayment/excelCollectPayment/$1';

$route['financeVendor/carRentalFeeCost/getDataAllCarVendor']['POST']							= 'FinanceVendor/CarRentalFeeCost/getDataAllCarVendor';
$route['financeVendor/carRentalFeeCost/getRecapCarRentalCostFee']['POST']						= 'FinanceVendor/CarRentalFeeCost/getRecapCarRentalCostFee';
$route['financeVendor/carRentalFeeCost/excelRecapCarRentalCostFee/(:any)/token']['GET']			= 'FinanceVendor/CarRentalFeeCost/excelRecapCarRentalCostFee/$1';
$route['financeVendor/carRentalFeeCost/getDetailCarRentalFee']['POST']							= 'FinanceVendor/CarRentalFeeCost/getDetailCarRentalFee';
$route['financeVendor/carRentalFeeCost/excelDetailCarRentalFee/(:any)/token']['GET']			= 'FinanceVendor/CarRentalFeeCost/excelDetailCarRentalFee/$1';
$route['financeVendor/carRentalFeeCost/getDetailCarRentalCost']['POST']							= 'FinanceVendor/CarRentalFeeCost/getDetailCarRentalCost';
$route['financeVendor/carRentalFeeCost/excelDetailCarRentalCost/(:any)/token']['GET']			= 'FinanceVendor/CarRentalFeeCost/excelDetailCarRentalCost/$1';
$route['financeVendor/carRentalFeeCost/getDetailCarRentalCostById']['POST']						= 'FinanceVendor/CarRentalFeeCost/getDetailCarRentalCostById';
$route['financeVendor/carRentalFeeCost/uploadCostReceipt']['POST']								= 'FinanceVendor/CarRentalFeeCost/uploadCostReceipt';
$route['financeVendor/carRentalFeeCost/saveCarCost']['POST']									= 'FinanceVendor/CarRentalFeeCost/saveCarCost';
$route['financeVendor/carRentalFeeCost/getDataCarRentalAdditionalCost']['POST']					= 'FinanceVendor/CarRentalFeeCost/getDataCarRentalAdditionalCost';

$route['report/agentPaymentBalance/getDataStatsAgentPayment']['POST']							= 'Report/AgentPaymentBalance/getDataStatsAgentPayment';

$route['report/reservationDetail/getDataReservationDetail']['POST']								= 'Report/ReservationDetail/getDataReservationDetail';
$route['report/recapPerProduct/getDataRecapPerProduct']['POST']									= 'Report/RecapPerProduct/getDataRecapPerProduct';

$route['report/recapPerDate/getDataRecapPerDate']['POST']										= 'Report/RecapPerDate/getDataRecapPerDate';
$route['report/recapPerDate/excelRecapPerDate/(:any)/token']['GET']								= 'Report/RecapPerDate/excelRecapPerDate/$1';

$route['setting/userLevel/getDataUserLevel']['POST']											= 'Setting/UserLevel/getDataUserLevel';
$route['setting/userLevel/insertDataUserLevel']['POST']											= 'Setting/UserLevel/insertDataUserLevel';
$route['setting/userLevel/updateDataUserLevel']['POST']											= 'Setting/UserLevel/updateDataUserLevel';

$route['setting/userLevelMenu/getDataLevelMenu']['POST']										= 'Setting/UserLevelMenu/getDataLevelMenu';
$route['setting/userLevelMenu/saveDataLevelMenu']['POST']										= 'Setting/UserLevelMenu/saveDataLevelMenu';

$route['setting/userAdmin/getDataUserAdmin']['POST']											= 'Setting/UserAdmin/getDataUserAdmin';
$route['setting/userAdmin/deleteUserAdmin']['POST']												= 'Setting/UserAdmin/deleteUserAdmin';
$route['setting/userAdmin/updateDataUserAdmin']['POST']											= 'Setting/UserAdmin/updateDataUserAdmin';
$route['setting/userAdmin/insertDataUserAdmin']['POST']											= 'Setting/UserAdmin/insertDataUserAdmin';

$route['setting/systemSetting/getDataSystemSetting']['POST']									= 'Setting/SystemSetting/getDataSystemSetting';
$route['setting/systemSetting/saveDataSystemSetting']['POST']									= 'Setting/SystemSetting/saveDataSystemSetting';

$route['setting/helpCenter/getDataHelpCenterContentList']['POST']								= 'Setting/HelpCenter/getDataHelpCenterContentList';
$route['setting/helpCenter/insertHelpCenterCategory']['POST']									= 'Setting/HelpCenter/insertHelpCenterCategory';
$route['setting/helpCenter/updateHelpCenterCategory']['POST']									= 'Setting/HelpCenter/updateHelpCenterCategory';
$route['setting/helpCenter/getDetailHelpCenterCategory']['POST']								= 'Setting/HelpCenter/getDetailHelpCenterCategory';
$route['setting/helpCenter/deleteHelpCenterCategory']['POST']									= 'Setting/HelpCenter/deleteHelpCenterCategory';
$route['setting/helpCenter/insertHelpCenterArticle']['POST']									= 'Setting/HelpCenter/insertHelpCenterArticle';
$route['setting/helpCenter/updateHelpCenterArticle']['POST']									= 'Setting/HelpCenter/updateHelpCenterArticle';
$route['setting/helpCenter/getDetailHelpCenterArticle']['POST']									= 'Setting/HelpCenter/getDetailHelpCenterArticle';
$route['setting/helpCenter/deleteHelpCenterArticle']['POST']									= 'Setting/HelpCenter/deleteHelpCenterArticle';

$route['settingPartner/partnerUserLevel/getDataUserLevel']['POST']								= 'SettingPartner/PartnerUserLevel/getDataUserLevel';
$route['settingPartner/partnerUserLevel/insertDataUserLevel']['POST']							= 'SettingPartner/PartnerUserLevel/insertDataUserLevel';
$route['settingPartner/partnerUserLevel/updateDataUserLevel']['POST']							= 'SettingPartner/PartnerUserLevel/updateDataUserLevel';

$route['settingPartner/userPartnerLevelMenu/getDataLevelMenu']['POST']							= 'SettingPartner/UserPartnerLevelMenu/getDataLevelMenu';
$route['settingPartner/userPartnerLevelMenu/saveDataLevelMenu']['POST']							= 'SettingPartner/UserPartnerLevelMenu/saveDataLevelMenu';

$route['settingPartner/userPartner/getDataUserPartner']['POST']									= 'SettingPartner/UserPartner/getDataUserPartner';
$route['settingPartner/userPartner/deleteUserPartner']['POST']									= 'SettingPartner/UserPartner/deleteUserPartner';
$route['settingPartner/userPartner/updateDataUserPartner']['POST']								= 'SettingPartner/UserPartner/updateDataUserPartner';
$route['settingPartner/userPartner/insertDataUserPartner']['POST']								= 'SettingPartner/UserPartner/insertDataUserPartner';

$route['knowledge/getDataKnowledge']['POST']													= 'Knowledge/getDataKnowledge';