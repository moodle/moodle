<?PHP // $Id$ 
      // install.php - created with Moodle 1.4.4 (2004083140)


$string['admindirerror'] = 'არაკორექტულადაა მითითებული ადმინისტრატორის დირექტორია';
$string['admindirname'] = 'ადმინისტრატორის დირექტორია';
$string['admindirsetting'] = 'ზოგიერთი ”ვებჰოსტი” იყენებს /admin, როგორც სპეციალურ ბმულს კონტროლის პანელზე მისაღწევად,ან  სხვა რაიმესთვის, რაც იწვევს კონფლიქტს Moodle-ის ადმინისტრატორის გვერდის სტანდარტული მდებარეობისათვის. ეს შეიძლება გასწორდეს ადმინისტრატორის დირექტორიის დატოვებით თქვენი ინსტალირებისას, და ახალი სახელის აქ ჩასმით. მაგ:<br /> <br /><b>moodle-ადმინისტრატორი</b><br /> <br />
ეს გაასწორებს ადმინისტრატორის ბმულს Moodle-ში.';
$string['caution'] = 'გაფრთხილება';
$string['chooselanguage'] = 'აირჩიეთ ენა';
$string['compatibilitysettings'] = 'გასინჯეთ თქვენი PHP სეთინგები';
$string['configfilenotwritten'] = 'ინსტალაციის სკრიპტს არ შეუძლია  config.php ფაილის ავტომატური შექმნა, რომელიც შეიცავსთქვენს არჩეულ სეთინგებს. შესაძლოა იმიტომ, რომ Moodle-ს დირექტორია არ არის ჩაწერადი. შეგიძლიათ  ხელით შეასრულოთ შემდეგი კოდების კოპირება დასახელებულ ფაილში config.php Moodle-ს ძირეული დირექტორიიდან.';
$string['configfilewritten'] = 'config.php წარმატებით შესრულდა';
$string['configurationcomplete'] = 'კონფიგურირება დასრულდა';
$string['database'] = 'მონაცემთა ბაზა';
$string['databasesettings'] = 'ახლა გესაჭიროებათ მონაცემთა ბაზის კონფიგურირება, სადაც შეინახება Moodle-ის უმეტესი მონაცემები. ეს მონაცემთა ბაზა უკვე უნდა იყოს შექმნილი და არსებობდეს მომხმარებლი სახელი და პაროლი მასთან მისაწვდომად<br />
<br /> <br />
<b>აკრიფეთ:</b> mysql ან postgres7<br />
<b>Host:</b> მაგ. localhost ან db.isp.com<br />
<b>სახელი:</b> მონაცემთა ბაზის სახელი, მაგ. საბას მადლი<br />
<b>მომხმარებელი:</b> თქვენი მონაცემთა ბაზის მომხმარებლის სახელი <br />
<b>პაროლი:</b> თქვენი მონაცემთა ბაზის პაროლი<br />
<b>ცხრილის პრეფიქსი:</b> არჩეული პრეფიქსი, ცხრილში ყველა სახელისათვის გამოსაყენებლად';
$string['dataroot'] = 'მონაცემტა დირექტორია';
$string['datarooterror'] = 'შეუძლებელია თქვენს მიერ მითითებული ”მონაცემთა დირექტორიის” შექმნა ან მოძიება. ან შეასწორეთ მითითებული გზა ,ან ხელით შექმენით ეს დირექტორია.';
$string['dbconnectionerror'] = 'ვერ შევძელით თქვენს მიერ მითითებულ მონაცემებთან დაკავშირება. გადახედეთ თქვენი მონაცემთა ბაზის სეთინგებს.';
$string['dbcreationerror'] = 'მონაცემთა ბაზის შექმნის შეცდომა. არსებული სეთინგის პირობებში შეუძლებელია მოცემული სახელის მონაცემთა ბაზის შექმნა';
$string['dbhost'] = 'ჰოსტი სერვერი';
$string['dbpass'] = 'პაროლი';
$string['dbprefix'] = 'ცხრილების პრეფიქსი';
$string['dbtype'] = 'სახეობა';
$string['directorysettings'] = '<p>დაადასტურეთ Moodle-ის ინსტალირების მდებარეობა.</p>

<p><b>Web მისამართი:</b>
მიუთითეთ სრული web მისამართი,სადაც Moodle მისაწვდომია. 
თუ web მისამართი მისაწვდომია მრავლობითი URL-იდან აირჩიეთ ისეთი, რომელიც იქნება უფრო ბუნებრივი თქვენი სტუდენტებისათვის. ნუ გამოიყენებთ მიყოლებულ დახრილ ხაზს.</p>

<p><b>Moodle-ს დირექტორია:</b>
მიუთითე ამ ინსტალირების დირექტორიის სრული გზა. დარწმუნდი, რომ სწორადაა არჩეული ასომთავრული.</p>

<p><b>მონაცემთა დირექტორია:</b>
აირჩიეთ ადგილი სადაც შეინახავთ Moodle-ს ატვირთულ ფაილებს. ეს დირექტორია უნდა იყოს ჩაწერადი და წაკითხვადი  web სერვერის მომხმარებლისთვის
(როგორც წესი \'nobody\' ან \'apache\'), მაგრამ ია არ უნდა იყოს მიღწევადი უშუალოდ web-იდან.</p>';
$string['dirroot'] = 'Moodle-ის დირექტორია';
$string['dirrooterror'] = '”Moodle-ის დირექტორიის” სეთინგი არასწორია. აქ არ არის Moodle-ის ინსტალაცია. ქვემოთ მოცემული მნისვნელობა ამოგდებულია.';
$string['download'] = 'ჩატვირთვა';
$string['fail'] = '”ჩაფლავდა”';
$string['fileuploads'] = 'ფაილის ატვირთვა';
$string['fileuploadserror'] = 'ეს უნდა იყოს';
$string['fileuploadshelp'] = '<p>როგორც ჩანს თქვენი სერვერიდან ფაილების ატვირთვა შეუძლებელია.</p>

<p>Moodle შეიძლება აიტვირთოს, მაგრამ ვერ მოხერხდება კურსების ატვირთვა ან ახალ მომხმარებელთაპროფილის ასახვა.</p>

<p>ფაილების ატვირთვის ნებადართვისათვის თქვენ (ან სისტემის ადმინისტრატორს) დაგჭირდებათ თქვენს სისტემაში ძირითადი php.ini ფაილის რედაქტირება და სეთინგის შეცვლა <b>file_uploads</b> to \'1\'.</p>';
$string['gdversion'] = 'GD ვარიანტი';
$string['gdversionerror'] = 'უნდა არსებობდეს GD ბიბლიოთეკა გამოსახულებების შესაქმნელად და დასამუშავებლად';
$string['gdversionhelp'] = '<p>როგორც ჩანს თქვენს სისტემაში GD არ არის ინსტალირებულიe.</p>

<p>GD არის ბიბლიოთეკა, რომელიც ესაჭიროება PHP-ს ნება დართოს Moodle-ს გამოსახულებების დასამუშავებლად
(მაგალითად მომხმარებელთა სურათები მათ პროფილში) და შესაქმნელად (როგორიცაა ლოგოები). Moodle იმუშავებს GD-ს გარეშეც, თუმცა ის შესაძლებლობები არარ გექნებათ.</p>

<p> GD-ს დასამატებლად PHP-ზე  Unix-ში, შეასრულეთ PHP გამოიყენებთ რა --with-gd პარამეტრს.</p>

<p> Windows გარემოში ჩვეულებრივად შესაძლოა php.ini-ს რედაქტირება და libgd.dll უკომენტაროდ დატოვება უკომენტაროდ .</p>';
$string['installation'] = 'ინსტალირება';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'ეს უნდა იყოს ამორთული';
$string['magicquotesruntimehelp'] = '<p>Magic quotes runtime should be turned off for Moodle to function properly.</p>

<p>Normally it is off by default ... see the setting <b>magic_quotes_runtime</b> in your php.ini file.</p>

<p>If you don\'t have access to your php.ini, you might be able to place the following line in a file 
called .htaccess within your Moodle directory:
<blockquote>php_value magic_quotes_runtime Off</blockquote>
</p> ';
$string['memorylimit'] = 'მახსოვრობის ზღვარი';
$string['memorylimiterror'] = 'არჩეულია ძალიან დაბალი PHP მახსოვრობის ზღვარი ... შემდგომში პრობლემები გექნებათ';
$string['memorylimithelp'] = '<p>The PHP memory limit for your server is currently set to $a.</p>

<p>This may cause Moodle to have memory problems later on, especially 
if you have a lot of modules enabled and/or a lot of users.</p>

<p>We recommend that you configure PHP with a higher limit if possible, like 16M. 
There are several ways of doing this that you can try:</p>
<ol>
<li>If you are able to, recompile PHP with <i>--enable-memory-limit</i>. 
This will allow Moodle to set the memory limit itself.</li>
<li>If you have access to your php.ini file, you can change the <b>memory_limit</b> 
setting in there to something like 16M. If you don\'t have access you might 
be able to ask your administrator to do this for you.</li>
<li>On some PHP servers you can create a .htaccess file in the Moodle directory 
containing this line:
<p><blockquote>php_value memory_limit 16M</blockquote></p>
<p>However, on some servers this will prevent <b>all</b> PHP pages from working 
(you will see errors when you look at pages) so you\'ll have to remove the .htaccess file.</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP არ არის სწორად კონფიგურირებული MySQL გაფართოებიდან, რათა ის უკავშირდებოდეს MySQL-ს. გადასინჯეთ php.ini ფაილი ან შეასრულეთ PHP-ის რეკომპილაცია.';
$string['pass'] = 'გადაცემა';
$string['phpversion'] = 'PHP ვარიანტი';
$string['phpversionerror'] = 'PHP-ის ვერსია უნდა იყოს არა ნაკლები ვიდრე 4.1.0';
$string['phpversionhelp'] = '<p>Moodle requires a PHP version of at least 4.1.0.</p>
<p>You are currently running version $a</p>
<p>You must upgrade PHP or move to a host with a newer version of PHP!</p>';
$string['safemode'] = 'უსაფრთხო ვარიანტი';
$string['safemodeerror'] = 'Moodle-ს შეიძლება ჰქონდეს პრობლემები ”დაცულ ';
$string['safemodehelp'] = '<p>Moodle may have a variety of problems with safe mode on, not least is that 
it probably won\'t be allowed to create new files.</p>

<p>Safe mode is usually only enabled by paranoid public web hosts, so you may have 
to just find a new web hosting company for your Moodle site.</p>

<p>You can try continuing the install if you like, but expect a few problems later on.</p>';
$string['sessionautostart'] = 'სესიის ავტომატური დასაწყისი';
$string['sessionautostarterror'] = 'ეს უნდა იყოს ამორთული';
$string['sessionautostarthelp'] = '<p>Moodle საჭიროებს სესიის მხარდაჭერას,რის გარეშე ის ვერ იმუშავებს გამართულად.</p>

<p>სესია სეიძლება ნებადართული იქნას php.ini ფაილით... მოძებნეთ პარამეტრი session.auto_start .</p>';
$string['wwwroot'] = 'Web მისამართი';
$string['wwwrooterror'] = 'ეს  web მისამართი არ უნდა იყოს სწორი. აქ არ არის Moodle-ის ინსტალაცია.';

?>
