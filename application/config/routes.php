<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Loginclass/index';
$route['404_override'] = 'ErrorClass/error404';
$route['translate_uri_dashes'] = FALSE;

$route['validaterequest'] = 'Loginclass/validaterequest';
$route['logout'] = 'Loginclass/logout';


$route['srview'] = 'Loginclass/srview';

/*reset password*/
$route['resetpassword'] = 'Resetpasswordclass/resetpassword';
$route['resetpasswordsend'] = 'Resetpasswordclass/resetpasswordsend';
$route['resetpassword/newform/(:any)/(:any)'] = 'Resetpasswordclass/resetpasswordnewform/$1/$2';
$route['updatepassword'] = 'Resetpasswordclass/updatepassword';


$route['sractiontakensave'] = 'Loginclass/sractiontakensave';

$route['servicerequest/srcheckconforme'] = 'Requestclass/srcheckconforme';
$route['servicerequest/conforme'] = 'Requestclass/conforme';
$route['servicerequest'] = 'Requestclass/servicerequest';
$route['servicerequest/dtrequestdtdef'] = 'Requestclass/dtrequestdtdef';
$route['servicerequest/dtrequestdtaccom'] = 'Requestclass/dtrequestdtaccom';
$route['servicerequest/dtrequestdtmyreq'] = 'Requestclass/dtrequestdtmyreq';
$route['servicerequest/dtrequestdtassigned'] = 'Requestclass/dtrequestdtassigned';
$route['servicerequest/getICTUEmployees'] = 'Requestclass/getICTUEmployees';
$route['servicerequest/assignRequest'] = 'Requestclass/assignRequest';
$route['servicerequest/reassignRequest'] = 'Requestclass/reassignRequest';
$route['servicerequest/srcancel'] = 'Requestclass/srcancel';
$route['servicerequest/srlogstatus'] = 'Requestclass/srlogstatus';
$route['servicerequest/sreditactiontaken'] = 'Requestclass/sreditactiontaken';
$route['servicerequest/srseleditactiontaken'] = 'Requestclass/srseleditactiontaken';
$route['servicerequest/srdeletelogstatus'] = 'Requestclass/srdeletelogstatus';
$route['servicerequest/sradditionalremarks'] = 'Requestclass/sradditionalremarks';
$route['servicerequest/srdeleteadditionalremarks'] = 'Requestclass/srdeleteadditionalremarks';


$route['servicerequest/srtbllibsrstatus'] = 'Requestclass/srtbllibsrstatus';

$route['servicerequestvidcon'] = 'RequestVidConclass/servicerequestvidcon';

$route['servicerequestview'] = 'Requestclass/servicerequestview';
$route['servicerequestvidconview'] = 'RequestVidConclass/servicerequestvidconview';

$route['servicerequest/subcategory'] = 'Requestclass/subcategory';
$route['servicerequest/actiontakensubcategory'] = 'Requestclass/actiontakensubcategory';

/* users */
/*$route['users'] = 'Userclass/index';
$route['users/dtuser'] = 'Userclass/dtuser';
$route['users/credentials'] = 'Userclass/credentials';*/

/* settings */
/*$route['settings'] = 'Settingsclass/index';
$route['settings/srchangepasssave'] = 'Settingsclass/srchangepasssave';
$route['settings/srchangecontactsave'] = 'Settingsclass/srchangecontactsave';*/



/*report*/
$route['report/srf'] = 'Reportclass/srfrep';
$route['report/print/srflist'] = 'Reportclass/repsrflist';

$route['report/srf-assignment-resolution'] = 'Reportclass/srfassignmentresolution';
$route['report/print/srfassignmentresolution'] = 'Reportclass/repsrfassignmentresolution';

$route['report/srfsummary'] = 'Reportclass/srfsummary';
$route['report/print/repsrfsummary'] = 'Reportclass/repsrfsummary';

$route['report/srfsummaryarta'] = 'Reportclass/srfsummaryarta';
$route['report/print/repsrfsummaryarta'] = 'Reportclass/repsrfsummaryarta';

$route['report/vidcon'] = 'Reportclass/vidcon';


$route['manual/vidcontutorial'] = 'Manualclass/vidcontutorial';
$route['manual/googleformtemplate'] = 'Manualclass/googleformtemplate';

//$route['servicerequest/srsave'] = 'Requestclass/srsave';


$route['errors/error405'] = 'Errortypeclass/error405';

/*library*/
$route['library/category'] = 'Libraryclass/category';
$route['library/dtcategory'] = 'Libraryclass/dtcategory';
$route['library/savecategory'] = 'Libraryclass/savecategory';
$route['library/editcategory'] = 'Libraryclass/editcategory';

$route['library/getcategory'] = 'Libraryclass/getcategory';

$route['library/subcategory'] = 'Libraryclass/subcategory';
$route['library/dtsubcategory'] = 'Libraryclass/dtsubcategory';
$route['library/savesubcategory'] = 'Libraryclass/savesubcategory';
$route['library/editsubcategory'] = 'Libraryclass/editsubcategory';
