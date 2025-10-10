<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
$url			=	$_SERVER['HTTP_HOST'];
$domain			=	explode(".",$_SERVER['HTTP_HOST']);
$subdomain		=	$domain[0];
$subdomain		=	$subdomain == $_ENV['DOMAIN_SUB_MAIN'] ? "" : $subdomain.".";
$productionURL	=	ENVIRONMENT == 'production' ? true : false;

$arrMonth		=	array(
	array("ID"=>"01", "VALUE"=>"January"),
	array("ID"=>"02", "VALUE"=>"February"),
	array("ID"=>"03", "VALUE"=>"March"),
	array("ID"=>"04", "VALUE"=>"April"),
	array("ID"=>"05", "VALUE"=>"May"),
	array("ID"=>"06", "VALUE"=>"June"),
	array("ID"=>"07", "VALUE"=>"July"),
	array("ID"=>"08", "VALUE"=>"August"),
	array("ID"=>"09", "VALUE"=>"September"),
	array("ID"=>"10", "VALUE"=>"October"),
	array("ID"=>"11", "VALUE"=>"November"),
	array("ID"=>"12", "VALUE"=>"December")
);
					
$arrYear		=	[];
$nextYear		=	date("Y", strtotime("+2 year"));
for($year = 2022; $year <= $nextYear; $year++){
	$arrYear[]	=	array("ID"=>$year, "VALUE"=>$year);
}
			
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

defined('DOMAIN_HTTP_TYPE')					OR define('DOMAIN_HTTP_TYPE', $_ENV['DOMAIN_HTTP_TYPE'] ?: 'http');
defined('DOMAIN_MAIN')						OR define('DOMAIN_MAIN', $_ENV['DOMAIN_MAIN'] ?: 'example.com');
defined('DOMAIN_SUB_MAIN')					OR define('DOMAIN_SUB_MAIN', $_ENV['DOMAIN_SUB_MAIN'] ?: 'sub.example.com');
defined('GOOGLE_TAG_MANAGER_ID')			OR define('GOOGLE_TAG_MANAGER_ID', $_ENV['GOOGLE_TAG_MANAGER_ID'] ?: 'G-GGS1C83JS9');

defined('SUBDOMAIN')						OR define('SUBDOMAIN', $subdomain);
defined('PRODUCTION_URL')					OR define('PRODUCTION_URL', $productionURL);
defined('BASE_URL')							OR define('BASE_URL', DOMAIN_HTTP_TYPE.'://'.$subdomain.DOMAIN_MAIN.'/');
defined('BASE_URL_ASSETS')					OR define('BASE_URL_ASSETS', '//'.$subdomain.DOMAIN_MAIN.'/assets/');
defined('BASE_URL_ASSETSAPI')				OR define('BASE_URL_ASSETSAPI', '//'.$subdomain.DOMAIN_MAIN.'/assetsapi/');
defined('BASE_URL_MOBILE_API')				OR define('BASE_URL_MOBILE_API', $_ENV['BASE_URL_MOBILE_API'] ?: 'https://mobile.example.com/');
defined('BASE_URL_ACCOUNTING_APP')			OR define('BASE_URL_ACCOUNTING_APP', $_ENV['BASE_URL_ACCOUNTING_APP'] ?: 'https://accounting.example.com/');
defined('BASE_URL_WHATSAPP_SYSTEM')			OR define('BASE_URL_WHATSAPP_SYSTEM', $_ENV['BASE_URL_WHATSAPP_SYSTEM'] ?: 'https://wa.example.com/');
defined('BASE_URL_WHATSAPP_REDIRECT')		OR define('BASE_URL_WHATSAPP_REDIRECT', BASE_URL_WHATSAPP_SYSTEM.$_ENV['BASE_URL_WHATSAPP_REDIRECT'] ?: 'access/redirect/');
defined('OPTION_MONTH')						OR define('OPTION_MONTH', json_encode($arrMonth));
defined('OPTION_YEAR')						OR define('OPTION_YEAR', json_encode($arrYear));

defined('ONESIGNAL_APP_ID')					OR define('ONESIGNAL_APP_ID', $_ENV['ONESIGNAL_APP_ID'] ?: 'dfsafadsf-asdf-asdf-asdf-asdfasdfasdf');
defined('FB_API_ACCESS_KEY')				OR define('FB_API_ACCESS_KEY', $_ENV['FB_API_ACCESS_KEY'] ?: 'EAAJZCZAOZCZAOZCZAOZCZAOZCZAOZCZAOZCZAOZCZAOZCZAOZCZAOZCZAOZCZAOZCZAOZCZAOZCZAOZCZAO');

defined('COMPANY_NAME')						OR define('COMPANY_NAME', $_ENV['COMPANY_NAME'] ?: 'Example Tours');
defined('COMPANY_ADDRESS')					OR define('COMPANY_ADDRESS', $_ENV['COMPANY_ADDRESS'] ?: 'Jl. Example no.123, Kuta, Bali, Indonesia');
defined('COMPANY_WEBSITE')					OR define('COMPANY_WEBSITE', $_ENV['COMPANY_WEBSITE'] ?: 'https://www.example.com');
defined('COMPANY_PHONE')					OR define('COMPANY_PHONE', $_ENV['COMPANY_PHONE'] ?: '+628123456789');
defined('COMPANY_EMAIL')					OR define('COMPANY_EMAIL', $_ENV['COMPANY_EMAIL'] ?: 'info@example.com');
defined('COMPANY_BANK_NAME')				OR define('COMPANY_BANK_NAME', $_ENV['COMPANY_BANK_NAME'] ?: 'Example PT.');
defined('COMPANY_BANK_PROVIDER')			OR define('COMPANY_BANK_PROVIDER', $_ENV['COMPANY_BANK_PROVIDER'] ?: 'Bank');
defined('COMPANY_BANK_ACCOUNT_NUMBER')		OR define('COMPANY_BANK_ACCOUNT_NUMBER', $_ENV['COMPANY_BANK_ACCOUNT_NUMBER'] ?: '123.456.7890');

defined('COMPANY_SOCMED_URL_FACEBOOK')		OR define('COMPANY_SOCMED_URL_FACEBOOK', $_ENV['COMPANY_SOCMED_URL_FACEBOOK'] ?: 'https://facebook.com/example');
defined('COMPANY_SOCMED_URL_TWITTER')		OR define('COMPANY_SOCMED_URL_TWITTER', $_ENV['COMPANY_SOCMED_URL_TWITTER'] ?: 'https://twitter.com/example');
defined('COMPANY_SOCMED_URL_LINKEDIN')		OR define('COMPANY_SOCMED_URL_LINKEDIN', $_ENV['COMPANY_SOCMED_URL_LINKEDIN'] ?: 'https://linkedin.com/company/example');
defined('COMPANY_SOCMED_URL_INSTAGRAM')		OR define('COMPANY_SOCMED_URL_INSTAGRAM', $_ENV['COMPANY_SOCMED_URL_INSTAGRAM'] ?: 'https://instagram.com/example');

defined('APP_NAME')							OR define('APP_NAME', $_ENV['APP_NAME'] ?: 'EXAMPLE TOURS - Admin');
defined('APP_PUBLIC_NAME')					OR define('APP_PUBLIC_NAME', $_ENV['APP_PUBLIC_NAME'] ?: 'EXAMPLE TOURS');
defined('APP_WHATSAPP_DATABASE_NAME')		OR define('APP_WHATSAPP_DATABASE_NAME', $_ENV['APP_WHATSAPP_DATABASE_NAME'] ?: 'db_dbwhatsapp');
defined('APP_OLD_DATABASE_NAME')			OR define('APP_OLD_DATABASE_NAME', $_ENV['APP_OLD_DATABASE_NAME'] ?: 'db_dbold');
defined('MAX_LOGIN_LIFETIME')				OR define('MAX_LOGIN_LIFETIME', $_ENV['MAX_LOGIN_LIFETIME'] ?: 14400); //max login lifetime in seconds
defined('MAX_TIME_CONFIRM_LOAN_TRANS')		OR define('MAX_TIME_CONFIRM_LOAN_TRANS', $_ENV['MAX_TIME_CONFIRM_LOAN_TRANS'] ?: 3); //max time for driver to confirm loan transcation (in hours)
defined('PASSWORD_DEFAULT')					OR define('PASSWORD_DEFAULT', $_ENV['PASSWORD_DEFAULT'] ?: 'iadug018DFJKA09763jd'); //default password for new user
defined('LOGIN_TOKEN_LENGTH')				OR define('LOGIN_TOKEN_LENGTH', $_ENV['LOGIN_TOKEN_LENGTH'] ?: 30); //login token char length
defined('LOGIN_TOKEN_MAXAGE_SECONDS')		OR define('LOGIN_TOKEN_MAXAGE_SECONDS', $_ENV['LOGIN_TOKEN_MAXAGE_SECONDS'] ?: 300); //login token max age in seconds
defined('LOGIN_TOKEN_MAXAGE_DIFF')			OR define('LOGIN_TOKEN_MAXAGE_DIFF', $_ENV['LOGIN_TOKEN_MAXAGE_DIFF'] ?: 60); //login token max age difference tolerance in seconds
defined('DEFAULT_KEY_ENCRYPTION')			OR define('DEFAULT_KEY_ENCRYPTION', $_ENV['DEFAULT_KEY_ENCRYPTION'] ?: 'daRTW90'); //key enkripsi default, JANGAN DIGANTI
defined('DEFAULT_DRIVER_PIN')				OR define('DEFAULT_DRIVER_PIN', $_ENV['DEFAULT_DRIVER_PIN'] ?: '1234'); //default driver secret PIN
defined('DEFAULT_VENDOR_PIN')				OR define('DEFAULT_VENDOR_PIN', $_ENV['DEFAULT_VENDOR_PIN'] ?: '4567'); //default vendor secret PIN
defined('MAILBOX_USERNAME')					OR define('MAILBOX_USERNAME', $_ENV['MAILBOX_USERNAME'] ?: 'username@gmail.com'); //username gmail
defined('MAILBOX_PASSWORD')					OR define('MAILBOX_PASSWORD', $_ENV['MAILBOX_PASSWORD'] ?: 'password'); //password gmail
defined('MAX_DAY_ADDITIONAL_COST_INPUT')	OR define('MAX_DAY_ADDITIONAL_COST_INPUT', $_ENV['MAX_DAY_ADDITIONAL_COST_INPUT'] ?: 10);
defined('MIN_CHARITY_NOMINAL')				OR define('MIN_CHARITY_NOMINAL', $_ENV['MIN_CHARITY_NOMINAL'] ?: 30000);
defined('CRON_RECONFIRMATION_WHATSAPP')		OR define('CRON_RECONFIRMATION_WHATSAPP', $_ENV['CRON_RECONFIRMATION_WHATSAPP'] ?: true);

defined('PATH_ASSETS')									OR define('PATH_ASSETS', FCPATH.$_ENV['PATH_ASSETS'] ?: 'assets/');
defined('PATH_STORAGE')									OR define('PATH_STORAGE', $_ENV['PATH_STORAGE'] ?: '/home/storage/');
defined('PATH_IMAGE_BST')								OR define('PATH_IMAGE_BST', PATH_STORAGE.$_ENV['PATH_IMAGE_BST'] ?: 'BST/');
defined('PATH_TMP_FILE')								OR define('PATH_TMP_FILE', PATH_STORAGE.$_ENV['PATH_TMP_FILE'] ?: 'tmp/');
defined('PATH_BANK_LOGO')								OR define('PATH_BANK_LOGO', PATH_STORAGE.$_ENV['PATH_BANK_LOGO'] ?: 'LogoBank/');
defined('PATH_TRANSFER_RECEIPT')						OR define('PATH_TRANSFER_RECEIPT', PATH_STORAGE.$_ENV['PATH_TRANSFER_RECEIPT'] ?: 'TransferReceipt/');
defined('PATH_EMAIL_HTML_FILE')							OR define('PATH_EMAIL_HTML_FILE', PATH_STORAGE.$_ENV['PATH_EMAIL_HTML_FILE'] ?: 'emailHTML/');
defined('PATH_EMAIL_RECONFIRMATION_FILE')				OR define('PATH_EMAIL_RECONFIRMATION_FILE', PATH_STORAGE.$_ENV['PATH_EMAIL_RECONFIRMATION_FILE'] ?: 'emailReconfirmation/');
defined('PATH_EMAIL_RECONFIRMATION_THREAD')				OR define('PATH_EMAIL_RECONFIRMATION_THREAD', PATH_STORAGE.$_ENV['PATH_EMAIL_RECONFIRMATION_THREAD'] ?: 'emailReconfirmationThread/');
defined('PATH_EMAIL_RECONFIRMATION_THREAD_ATTACHMENT')	OR define('PATH_EMAIL_RECONFIRMATION_THREAD_ATTACHMENT', PATH_STORAGE.$_ENV['PATH_EMAIL_RECONFIRMATION_THREAD_ATTACHMENT'] ?: 'emailReconfirmationThread/attachment/');
defined('PATH_INVOICE_HTML_FILE')						OR define('PATH_INVOICE_HTML_FILE', PATH_STORAGE.$_ENV['PATH_INVOICE_HTML_FILE'] ?: 'invoice/');
defined('PATH_VOUCHER_FILE')							OR define('PATH_VOUCHER_FILE', PATH_STORAGE.$_ENV['PATH_VOUCHER_FILE'] ?: 'voucher/');
defined('PATH_EXCEL_TRANSFER_LIST_FILE')				OR define('PATH_EXCEL_TRANSFER_LIST_FILE', PATH_STORAGE.$_ENV['PATH_EXCEL_TRANSFER_LIST_FILE'] ?: 'xlsxTransferList/');
defined('PATH_HTML_TRANSFER_RECEIPT')					OR define('PATH_HTML_TRANSFER_RECEIPT', PATH_STORAGE.$_ENV['PATH_HTML_TRANSFER_RECEIPT'] ?: 'transferReceiptHTML/');
defined('PATH_MANUAL_WITHDRAW_VENDOR_DOCUMENT')			OR define('PATH_MANUAL_WITHDRAW_VENDOR_DOCUMENT', PATH_STORAGE.$_ENV['PATH_MANUAL_WITHDRAW_VENDOR_DOCUMENT'] ?: 'manualWithdrawVendorDocument/');
defined('PATH_KNOWLEDGE_FILE')							OR define('PATH_KNOWLEDGE_FILE', PATH_STORAGE.$_ENV['PATH_KNOWLEDGE_FILE'] ?: 'Knowledge/');
defined('PATH_SOURCE_LOGO')								OR define('PATH_SOURCE_LOGO', PATH_IMAGE_BST.$_ENV['PATH_SOURCE_LOGO'] ?: 'sourceLogo/');
defined('PATH_STORAGE_COLLECT_PAYMENT_RECEIPT')			OR define('PATH_STORAGE_COLLECT_PAYMENT_RECEIPT', PATH_IMAGE_BST.$_ENV['PATH_STORAGE_COLLECT_PAYMENT_RECEIPT'] ?: 'collectPayment/');
defined('PATH_STORAGE_ADDITIONAL_COST_IMAGE')			OR define('PATH_STORAGE_ADDITIONAL_COST_IMAGE', PATH_IMAGE_BST.$_ENV['PATH_STORAGE_ADDITIONAL_COST_IMAGE'] ?: 'additionalCost/');
defined('PATH_STORAGE_ADDITIONAL_INCOME_IMAGE')			OR define('PATH_STORAGE_ADDITIONAL_INCOME_IMAGE', PATH_IMAGE_BST.$_ENV['PATH_STORAGE_ADDITIONAL_INCOME_IMAGE'] ?: 'additionalIncome/');
defined('PATH_REIMBURSEMENT_RECEIPT')					OR define('PATH_REIMBURSEMENT_RECEIPT', PATH_IMAGE_BST.$_ENV['PATH_REIMBURSEMENT_RECEIPT'] ?: 'reimbursement/');
defined('PATH_CAR_COST_RECEIPT')						OR define('PATH_CAR_COST_RECEIPT', PATH_IMAGE_BST.$_ENV['PATH_CAR_COST_RECEIPT'] ?: 'carCostReceipt/');

defined('URL_SOURCE_LOGO')							OR define('URL_SOURCE_LOGO', BASE_URL.$_ENV['URL_SOURCE_LOGO'] ?: 'foto/sourceLogo/');
defined('URL_BANK_LOGO')							OR define('URL_BANK_LOGO', BASE_URL.$_ENV['URL_BANK_LOGO'] ?: 'foto/bankLogo/');
defined('URL_TRANSFER_RECEIPT')						OR define('URL_TRANSFER_RECEIPT', BASE_URL.$_ENV['URL_TRANSFER_RECEIPT'] ?: 'foto/transferReceipt/');
defined('URL_MAIL_PREVIEW')							OR define('URL_MAIL_PREVIEW', BASE_URL.$_ENV['URL_MAIL_PREVIEW'] ?: 'mailbox/getPreviewMail/');
defined('URL_MAIL_RECONFIRMATION_CRON_SEND')		OR define('URL_MAIL_RECONFIRMATION_CRON_SEND', BASE_URL.$_ENV['URL_MAIL_RECONFIRMATION_CRON_SEND'] ?: 'cron/sendMailReconfirmation/');
defined('URL_MAIL_RECONFIRMATION_PREVIEW')			OR define('URL_MAIL_RECONFIRMATION_PREVIEW', BASE_URL.$_ENV['URL_MAIL_RECONFIRMATION_PREVIEW'] ?: 'reconfirmation/getPreviewReconfirmationMail/');
defined('URL_MAIL_RECONFIRMATION_DRAFT_PREVIEW')	OR define('URL_MAIL_RECONFIRMATION_DRAFT_PREVIEW', BASE_URL.$_ENV['URL_MAIL_RECONFIRMATION_DRAFT_PREVIEW'] ?: 'cron/previewMailReconfirmation/');
defined('URL_CAR_COST_RECEIPT')						OR define('URL_CAR_COST_RECEIPT', BASE_URL.$_ENV['URL_CAR_COST_RECEIPT'] ?: 'foto/carCostReceipt/');
defined('URL_REIMBURSEMENT_IMAGE')					OR define('URL_REIMBURSEMENT_IMAGE', BASE_URL.$_ENV['URL_REIMBURSEMENT_IMAGE'] ?: 'foto/reimbursement/');
defined('URL_MAIL_RECONFIRMATION_ATTACHMENT')		OR define('URL_MAIL_RECONFIRMATION_ATTACHMENT', BASE_URL_ASSETSAPI.$_ENV['URL_MAIL_RECONFIRMATION_ATTACHMENT'] ?: 'mailReconfirmationAttachment/');
defined('URL_ADDITIONAL_COST_IMAGE')				OR define('URL_ADDITIONAL_COST_IMAGE', BASE_URL_MOBILE_API.$_ENV['URL_ADDITIONAL_COST_IMAGE'] ?: '/additionalCost/imageAdditionalCost/');
defined('URL_ADDITIONAL_INCOME_IMAGE')				OR define('URL_ADDITIONAL_INCOME_IMAGE', BASE_URL_MOBILE_API.$_ENV['URL_ADDITIONAL_INCOME_IMAGE'] ?: '/additionalIncome/imageAdditionalIncome/');
defined('URL_COLLECT_PAYMENT_RECEIPT')				OR define('URL_COLLECT_PAYMENT_RECEIPT', BASE_URL_MOBILE_API.$_ENV['URL_COLLECT_PAYMENT_RECEIPT'] ?: '/collectPayment/imageSettlementCollectPayment/');
defined('URL_RESEVATION_INVOICE_FILE')				OR define('URL_RESEVATION_INVOICE_FILE', DOMAIN_HTTP_TYPE.'://'.$subdomain.DOMAIN_MAIN.($_ENV['URL_RESEVATION_INVOICE_FILE'] ?: '/file/reservationInvoice/'));
defined('URL_RESEVATION_VOUCHER_FILE')				OR define('URL_RESEVATION_VOUCHER_FILE', DOMAIN_HTTP_TYPE.'://'.$subdomain.DOMAIN_MAIN.($_ENV['URL_RESEVATION_VOUCHER_FILE'] ?: '/file/reservationVoucher/'));
defined('URL_EXCEL_TRANSFER_LIST_FILE')				OR define('URL_EXCEL_TRANSFER_LIST_FILE', DOMAIN_HTTP_TYPE.'://'.$subdomain.DOMAIN_MAIN.($_ENV['URL_EXCEL_TRANSFER_LIST_FILE'] ?: '/file/xlsxTransferList/'));
defined('URL_HTML_TRANSFER_RECEIPT')				OR define('URL_HTML_TRANSFER_RECEIPT', DOMAIN_HTTP_TYPE.'://'.$subdomain.DOMAIN_MAIN.($_ENV['URL_HTML_TRANSFER_RECEIPT'] ?: '/file/transferReceiptHTML/'));
defined('URL_KNOWLEDGE_FILE')						OR define('URL_KNOWLEDGE_FILE', DOMAIN_HTTP_TYPE.'://'.$subdomain.DOMAIN_MAIN.($_ENV['URL_KNOWLEDGE_FILE'] ?: '/file/knowledge/'));

defined('MAILREVIEW_URL_DEFAULT')					OR define('MAILREVIEW_URL_DEFAULT', $_ENV['MAILREVIEW_URL_DEFAULT'] ?: 'https://www.tripadvisor.co.id/Bali.html');
defined('MAILREVIEW_WHATSAPP_NUMBER')				OR define('MAILREVIEW_WHATSAPP_NUMBER', $_ENV['MAILREVIEW_WHATSAPP_NUMBER'] ?: '6285123456789');
defined('MAILREVIEW_PHONE_NUMBER')					OR define('MAILREVIEW_PHONE_NUMBER', $_ENV['MAILREVIEW_PHONE_NUMBER'] ?: '+6285123456789');
defined('MAILREVIEW_EMAIL_CUSTOMERSERVICE')			OR define('MAILREVIEW_EMAIL_CUSTOMERSERVICE', $_ENV['MAILREVIEW_EMAIL_CUSTOMERSERVICE'] ?: 'cs@example.com');

defined('REVIEW_URL_DEFAULT_KLOOK')					OR define('REVIEW_URL_DEFAULT_KLOOK', $_ENV['REVIEW_URL_DEFAULT_KLOOK'] ?: 'https://www.klook.com/my_reviews/?spm=Booking.MyReview');
defined('REVIEW_URL_DEFAULT_VIATOR')				OR define('REVIEW_URL_DEFAULT_VIATOR', $_ENV['REVIEW_URL_DEFAULT_VIATOR'] ?: 'https://www.tripadvisor.com/Bali.html');

defined('MAIL_KLIKBCAPAYROLL_ADDRESS')				OR define('MAIL_KLIKBCAPAYROLL_ADDRESS', $_ENV['MAIL_KLIKBCAPAYROLL_ADDRESS'] ?: 'info@example.com');
defined('MAIL_KLOOK_BAD_REVIEW_SENDER_ADDRESS')		OR define('MAIL_KLOOK_BAD_REVIEW_SENDER_ADDRESS', $_ENV['MAIL_KLOOK_BAD_REVIEW_SENDER_ADDRESS'] ?: 'no-reply@example.com');

defined('MAIL_HOST')								OR define('MAIL_HOST', $_ENV['MAIL_HOST'] ?: 'smtp.gmail.com');
defined('MAIL_NAME')								OR define('MAIL_NAME', $_ENV['MAIL_NAME'] ?: 'Customer Service');
defined('MAIL_USERNAME')							OR define('MAIL_USERNAME', $_ENV['MAIL_USERNAME'] ?: 'cs@example.net');
defined('MAIL_PASSWORD')							OR define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD'] ?: 'dsfdfasfasdfasf');
defined('MAIL_FROMADDRESS')							OR define('MAIL_FROMADDRESS', $_ENV['MAIL_FROMADDRESS'] ?: 'cs@example.com');
defined('MAIL_SMTPPORT')							OR define('MAIL_SMTPPORT', $_ENV['MAIL_SMTPPORT'] ?: 465);
defined('MAIL_IMAPPORT')							OR define('MAIL_IMAPPORT', $_ENV['MAIL_IMAPPORT'] ?: 993);

defined('FIREBASE_MOBILE_SERVICE_ACCOUNT_KEY_PATH')	OR define('FIREBASE_MOBILE_SERVICE_ACCOUNT_KEY_PATH', FCPATH.$_ENV['FIREBASE_MOBILE_SERVICE_ACCOUNT_KEY_PATH'] ?: 'apps/config/firebase-adminsdk.json');
defined('FIREBASE_MOBILE_PROJECT_ID')				OR define('FIREBASE_MOBILE_PROJECT_ID', $_ENV['FIREBASE_MOBILE_PROJECT_ID'] ?: 'project-id');

defined('FIREBASE_PRIVATE_KEY_PATH')				OR define('FIREBASE_PRIVATE_KEY_PATH', FCPATH . $_ENV['FIREBASE_PRIVATE_KEY_PATH'] ?: 'apps/config/firebase-adminsdk.json');
defined('FIREBASE_RTDB_API_KEY')					OR define('FIREBASE_RTDB_API_KEY', $_ENV['FIREBASE_RTDB_API_KEY'] ?: '28dfsaikj32j4lk3j4l23j4l23j4l23j4l23j4');
defined('FIREBASE_RTDB_AUTH_DOMAIN')				OR define('FIREBASE_RTDB_AUTH_DOMAIN', $_ENV['FIREBASE_RTDB_AUTH_DOMAIN'] ?: 'app.firebaseapp.com');
defined('FIREBASE_RTDB_URI')						OR define('FIREBASE_RTDB_URI', $_ENV['FIREBASE_RTDB_URI'] ?: 'https://rtdb.asia-southeast1.firebasedatabase.app/');
defined('FIREBASE_RTDB_PROJECT_ID')					OR define('FIREBASE_RTDB_PROJECT_ID', $_ENV['FIREBASE_RTDB_PROJECT_ID'] ?: 'web-app');
defined('FIREBASE_RTDB_STORAGE_BUCKET')				OR define('FIREBASE_RTDB_STORAGE_BUCKET', $_ENV['FIREBASE_RTDB_STORAGE_BUCKET'] ?: 'app.appspot.com');
defined('FIREBASE_RTDB_MESSAGING_SENDER_ID')		OR define('FIREBASE_RTDB_MESSAGING_SENDER_ID', $_ENV['FIREBASE_RTDB_MESSAGING_SENDER_ID'] ?: '1234567890');
defined('FIREBASE_RTDB_APPLICATION_ID')				OR define('FIREBASE_RTDB_APPLICATION_ID', $_ENV['FIREBASE_RTDB_APPLICATION_ID'] ?: '1:31482734517:web:234FGSHSDFGSDFG');
defined('FIREBASE_RTDB_MEASUREMENT_ID')				OR define('FIREBASE_RTDB_MEASUREMENT_ID', $_ENV['FIREBASE_RTDB_MEASUREMENT_ID'] ?: 'G-ABCDEFGHIJ');
defined('FIREBASE_RTDB_MAINREF_NAME')				OR define('FIREBASE_RTDB_MAINREF_NAME', $_ENV['FIREBASE_RTDB_MAINREF_NAME'] ?: 'webapp-statisticTags/');
defined('FIREBASE_RTDB_MAINREF_NAME_PARTNER')		OR define('FIREBASE_RTDB_MAINREF_NAME_PARTNER', $_ENV['FIREBASE_RTDB_MAINREF_NAME_PARTNER'] ?: 'webapp-partner/');
defined('FIREBASE_RTDB_MAILREF_NAME')				OR define('FIREBASE_RTDB_MAILREF_NAME', FIREBASE_RTDB_MAINREF_NAME.$_ENV['FIREBASE_RTDB_MAILREF_NAME'] ?: 'unprocessedReservationMail');

defined('ROKET_ECOMMERCE_PRIVATE_KEY')				OR define('ROKET_ECOMMERCE_PRIVATE_KEY', $_ENV['ROKET_ECOMMERCE_PRIVATE_KEY'] ?: 'PRODUCTION-PRIVATE-KEY');
defined('ROKET_ECOMMERCE_PUBLIC_KEY')				OR define('ROKET_ECOMMERCE_PUBLIC_KEY', $_ENV['ROKET_ECOMMERCE_PUBLIC_KEY'] ?: 'PRODUCTION-PUBLIC-KEY');
defined('ROKET_ECOMMERCE_API_BASE_URL')				OR define('ROKET_ECOMMERCE_API_BASE_URL', $_ENV['ROKET_ECOMMERCE_API_BASE_URL'] ?: 'https://be.example.com');

defined('ROKET_CS_AI_AGENT_PRIVATE_KEY')			OR define('ROKET_CS_AI_AGENT_PRIVATE_KEY', $_ENV['ROKET_CS_AI_AGENT_PRIVATE_KEY'] ?: 'PRODUCTION-PRIVATE-KEY');
defined('ROKET_CS_AI_AGENT_PUBLIC_KEY')				OR define('ROKET_CS_AI_AGENT_PUBLIC_KEY', $_ENV['ROKET_CS_AI_AGENT_PUBLIC_KEY'] ?: 'PRODUCTION-PUBLIC-KEY');
defined('ROKET_CS_AI_AGENT_BASE_URL')				OR define('ROKET_CS_AI_AGENT_BASE_URL', $_ENV['ROKET_CS_AI_AGENT_BASE_URL'] ?: 'https://example.com/api/');
defined('ROKET_CS_AI_AGENT_SURCHARGE_COVERAGE_URL')	OR define('ROKET_CS_AI_AGENT_SURCHARGE_COVERAGE_URL', ROKET_CS_AI_AGENT_BASE_URL.$_ENV['ROKET_CS_AI_AGENT_SURCHARGE_COVERAGE_URL'] ?: 'get-coverage-surcharge');
defined('ROKET_CS_AI_AGENT_BAD_REVIEW_ANALYZE_URL')	OR define('ROKET_CS_AI_AGENT_BAD_REVIEW_ANALYZE_URL', ROKET_CS_AI_AGENT_BASE_URL.$_ENV['ROKET_CS_AI_AGENT_BAD_REVIEW_ANALYZE_URL'] ?: 'get-bad-review-analyze');

defined('SEPCIAL_CASE_COST_RULES')					OR define('SEPCIAL_CASE_COST_RULES', '[
	{
		"title" : "Additional Monkey Saturday and Sunday",
		"minTotalPoint" : 2,
		"rules": [
			{
				"fields" : ["NRESERVATIONDATESTART"],
				"condition" : "in_array",
				"days" : [6,7]
			},
			{
				"fields" : ["RESERVATIONTITLE"],
				"condition" : "include_string",
				"strings":[
					"UbudTour",
					"UbudFullTour",
					"Monkey",
					"MonkeyForest",
					"UbudDayTour",
					"UbudPrivateTour",
					"BestofBali-AllInclusive",
					"ShortTour",
					"ShorterTour",
					"UbudBaliTour",
					"UbudPrivateDayTour",
					"Ubud-Fulltour",
					"UbudDayTrip",
					"UbudTopAttractions",
					"UbudHighlight",
					"TopDestinationofBali",
					"Ubudand"
				]
			}
		],
		"warningMessage": "* Monkey +20K/Pax khusus hari sabtu dan minggu"
	},
	{
		"title" : "Additional Entrance Benoa Port",
		"minTotalPoint" : 1,
		"rules": [
			{
				"fields": ["HOTELNAME", "PICKUPLOCATION"],
				"condition" : "include_string",
				"strings" : ["BenoaPort", "BenoaCruiseTerminal", "BCT", "BaliCruiseShipTerminal"]
			}
		],
		"warningMessage": "* Additional 22k/car for port entrance"
	}
]'
);