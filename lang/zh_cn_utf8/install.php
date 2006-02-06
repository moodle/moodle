<?PHP // $Id$ 
      // install.php - created with Moodle 1.6 development (2005101200)


$string['admindirerror'] = '指定的管理目录不正确';
$string['admindirname'] = '管理目录';
$string['admindirsetting'] = '有一些服务器的/admin用在了如控制面板之类的特殊功能上，但这与标准的Moodle管理页面冲突了。通过修改管理目录的名称并将新名称填写在这里就可以避免冲突了。例如: <br /> <br /><b>moodleadmin</b><br /> <br />
这将修正Moodle中的管理链接。';
$string['caution'] = '原因';
$string['chooselanguage'] = '选择一种语言';
$string['compatibilitysettings'] = '检查您的PHP设置...';
$string['configfilenotwritten'] = '安装脚本无法自动创建一个包含您设置的config.php文件，极可能是由于Moodle目录是不能写的。您可以复制如下的代码到Moodle根目录下的config.php文件中。';
$string['configfilewritten'] = '已经成功创建了config.php文件';
$string['configurationcomplete'] = '配置完毕';
$string['database'] = '数据库';
$string['databasecreationsettings'] = '现在您需要配置数据库选项，Moodle的大部分数据都是存储在数据库中的。Moodle4Windows安装程序会根据下面的选项自动为您创建这个数据库。<br />
<br /> <br />
<b>类型：</b>安装程序只允许“mysql”<br />
<b>主机：</b>安装程序只允许“localhost”<br />
<b>名称：</b>数据库名称，如moodle<br />
<b>用户名：</b>安装程序只允许“root”<br />
<b>密码：</b>您的数据库密码<br />
<b>表格前缀：</b>用于所有表格名的前缀(可选)';
$string['databasesettings'] = '现在您需要配置数据库了，多数的Moodle数据都将存储在其中。这个数据库必须已经存在了，并且必须有一个用户名和密码来访问它。<br /> <br /> <br />
<b>类型:</b> mysql或postgres7<br />
<b>主机:</b> 如localhost或db.isp.com<br />
<b>名称:</b> 数据库名称，如moodle<br />
<b>用户:</b> 访问数据库的用户名<br />
<b>密码:</b> 访问数据库的密码<br />
<b>表格前缀:</b> 在所有的表格名称前加上可选的前缀';
$string['dataroot'] = '数据目录';
$string['datarooterror'] = '找不到也无法创建您指定的“数据目录”，请更正路径或手工创建它。';
$string['dbconnectionerror'] = '无法连接到您指定的数据库，请检查您的数据库设置。';
$string['dbcreationerror'] = '数据库创建错误。无法用设定中的名称创建数据库。';
$string['dbhost'] = '服务器主机';
$string['dbpass'] = '密码';
$string['dbprefix'] = '表格名称前缀';
$string['dbtype'] = '类型';
$string['directorysettings'] = '<p>请确认安装Moodle的位置。</p>

<p><b>Web地址:</b>
指定访问Moodle的完整Web地址。如果您的网站可以通过多个URL访问，那么选择其中最常用的一个。地址的末尾不要有斜线。</p>

<p><b>Moodle目录:</b>
指定安装的完整路径，要确保大小写正确。</p>

<p><b>数据目录:</b>
Moodle需要一个位置存放上传的文件。这个目录对于Web服务器用户(通常是“nobody”或“apache”)应当是可读可写的，但应当不能直接通过Web访问它。</p>';
$string['dirroot'] = 'Moodle目录';
$string['dirrooterror'] = '“Moodle目录”的设置看上去不对——在那里找不到安装好的Moodle。下面的值已经重置了。';
$string['download'] = '下载';
$string['fail'] = '失败';
$string['fileuploads'] = '上传文件';
$string['fileuploadserror'] = '这应当是开启的';
$string['fileuploadshelp'] = '<p>这个服务器上的文件上传功能看上去被关闭了。</p>

<p>您可以继续安装Moodle，但没有这个功能，您将不能上传任何文件或用户头像。</p>

<p>要激活文件上传，您(或您的系统管理员)需要修改系统上的php.ini文件，并将其中<b>file_uploads</b>的设置改为\'1\'。</p>';
$string['gdversion'] = 'GD版本';
$string['gdversionerror'] = '为了能够处理和创建图片，服务器上必须有GD库。';
$string['gdversionhelp'] = '<p>您的服务器看上去并没有安装GD。</p>

<p>PHP要有GD库才能让Moodle处理图像(如用户图标)。没有GD，Moodle还是可以工作的——只是那些需要GD的功能就不能使用了。</p>

<p>在Unix上为PHP增加GD功能，可以用--with-gd选项来编译PHP。</p>

<p>在Windows上，修改php.ini并去掉libgd.dll行前的注释符号就可以了。</p>';
$string['globalsquotes'] = '处理全局变量的方式不安全';
$string['globalsquoteserror'] = '修正您的PHP设置：禁用register_globals和/或启动magic_quotes_gpc。';
$string['globalsquoteshelp'] = '<p>我们不建议你在禁用Magic Quotes GPC的同时开启Register Globals。</p>

<p>一种比较好的做法是在php.ini中设定<b>magic_quotes_gpc = On</b>和<b>register_globals = Off。</b></p>

<p>如果您无权访问您的php.ini，您可以在Moodle目录内的.htaccess文件中增加如下内容：
<blockquote>php_value magic_quotes_gpc On</blockquote>
<blockquote>php_value register_globals Off</blockquote>
</p>';
$string['installation'] = '安装';
$string['magicquotesruntime'] = '运行时的Magic Quotes';
$string['magicquotesruntimeerror'] = '这应该是关闭的';
$string['magicquotesruntimehelp'] = '<p>运行时的Magic Quotes应当关闭，这样Moodle才能正常工作。</p>

<p>通常缺省时它是关闭的...参考php.ini文件中的<b>magic_quotes_runtime</b>设置。</p>

<p>如果您不能访问php.ini文件，也许您可以把下面的内容添加到Moodle目录中名为.htaccess的文件中:</p>
<blockquote>php_value magic_quotes_runtime Off</blockquote>';
$string['memorylimit'] = '内存限制';
$string['memorylimiterror'] = 'PHP内存限制设置的太低了...以后您会遇到问题的。';
$string['memorylimithelp'] = '<p>您的服务器的PHP内存限制是${a}。</p>

<p>这会使Moodle在将来运行是碰到内存问题，特别是您安装了很多模块并且/或者有很多用户。</p>

<p>我们建议可能的话把限制设定的高一些，譬如16M。有几种方法可以做到这一点:</p>
<ol>
<li>如果可以，重新编译PHP并使用<i>--enable-memory-limit</i>选项。这允许Moodle自己设定内存限制。</li>
<li>如果可以访问php.ini文件，您可以修改<b>memory_limit</b>的设置为其它值如16M。如果您无法访问，可以让您的管理员帮您修改一下。</li>
<li>在一些PHP服务器上，您可以在Moodle目录中创建一个.htaccess文件并包含如下内容:
<blockquote>php_value memory_limit 16M</blockquote>
<p>然而，在一些服务器上这会让<b>所有</b>PHP页面无法正常工作(在访问页面时会有错误)，因此您可能不得不删除.htaccess文件。</p></li>
</ol>';
$string['mysqlextensionisnotpresentinphp'] = 'PHP的MySQL扩展并未安装正确，因此无法与MySQL通信。请检查您的php.ini文件或重新编译PHP。';
$string['pass'] = '通过';
$string['phpversion'] = 'PHP版本';
$string['phpversionerror'] = 'PHP版本至少为4.1.0';
$string['phpversionhelp'] = '<p>Moodle需要PHP 4.1.0以上的版本。</p>
<p>您当前使用的是${a}</p>
<p>您必须升级PHP或者转移到一个有新版PHP的服务器上!</p>';
$string['safemode'] = '安全模式';
$string['safemodeerror'] = '在安全模式下运行Moodle可能会有麻烦';
$string['safemodehelp'] = '<p>在安全模式下运行Moodle可能会遇到一系列的问题，至少在会无法创建新文件。</p>

<p>只有那些有安全妄想证的公共Web站点才会使用安全模式，因此如果遇到这个方面的麻烦，最好的方法就是为您的Moodle站点换一个Web主机提供商。</p>

<p>如果您喜欢可以继续安装过程，但将来会遇到问题的。</p>';
$string['sessionautostart'] = '自动开启会话';
$string['sessionautostarterror'] = '这应当是关闭的';
$string['sessionautostarthelp'] = '<p>Moodle需要会话支持，否则便无法正常工作。</p>

<p>通过修改php.ini文件可以激活会话支持...找找session.auto_start参数</p>';
$string['wwwroot'] = '网站地址';
$string['wwwrooterror'] = '这个网站地址似乎是错的——在那里并没有刚刚装好的Moodle。';

?>
