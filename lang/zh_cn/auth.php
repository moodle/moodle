<?PHP // $Id$ 
      // auth.php - created with Moodle 1.2 development (2003120700)


$string['auth_dbdescription'] = '该方法使用一个外部数据库来检验用户名和密码是否有效。如果是一个新帐号，该帐号其它字段的信息将一起复制到本系统中。';
$string['auth_dbextrafields'] = '这些字段是可选的。你在此指定的<B>外部数据库字段</B>将预先填入本系统的用户数据库中。<P>如果你留空不填，将使用系统默认值。<P>无论以上哪种情况，用户在登录后都可以改写这些字段。';
$string['auth_dbfieldpass'] = '含有密码的字段名';
$string['auth_dbfielduser'] = '含有用户名的字段名';
$string['auth_dbhost'] = '数据库所在的主机。';
$string['auth_dbname'] = '数据库名';
$string['auth_dbpass'] = '与上面的用户名匹配的密码';
$string['auth_dbpasstype'] = '指定密码字段所用的格式。MD5编码可用于与其它通用WEB应用如PostNuke相联接';
$string['auth_dbtable'] = '数据库中的表单名';
$string['auth_dbtitle'] = '使用一个外部数据库';
$string['auth_dbtype'] = '数据库类型（详情请看<A HREF=../lib/adodb/readme.htm#drivers>ADOdb帮助文档</A>）';
$string['auth_dbuser'] = '对该数据库具有读权限的用户名';
$string['auth_emaildescription'] = '电子邮件确认是默认的身份验证方法。用户注册时可以选用自己的用户名和密码，然后有一封确认信件发送到该用户的电子邮箱。该信件中有一个安全的链接指向用户确认帐号的页面。以后的登录就只根据本系统的数据库中储存的信息检验用户名和密码。';
$string['auth_emailtitle'] = '基于电子邮件的身份验证';
$string['auth_imapdescription'] = '该方法使用一个IMAP服务器来检验用户名和密码是否有效。';
$string['auth_imaphost'] = 'IMAP服务器地址。用IP地址，不要用域名。';
$string['auth_imapport'] = 'IMAP服务器端口号。通常是143或993。';
$string['auth_imaptitle'] = '使用一个IMAP服务器';
$string['auth_imaptype'] = 'IMAP服务器类型。IMAP服务器可能有不同类型的身份验证。';
$string['auth_ldap_bind_dn'] = '如果你想用绑定用户来搜索用户，在此指定。就象：‘cn=ldapuser,ou=public,o=org’';
$string['auth_ldap_bind_pw'] = '绑定用户的密码。';
$string['auth_ldap_contexts'] = '用户背景列表。以‘;’分隔。例如：‘ou=users,o=org; ou=others,o=org’';
$string['auth_ldap_create_context'] = '如果你允许根据email信息创建用户,指定创建用户的内容.该值应该有别于别的用户';
$string['auth_ldap_creators'] = '列出可创建新课程的组.用\';\'分割多个组.如\'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = '以URL形式指定LDAP主机，类似于：‘ldap://ldap.myorg.com/’或‘ldaps://ldap.myorg.com/’';
$string['auth_ldap_memberattribute'] = '指定从属于某个组的用户属性,一般是\'member\'';
$string['auth_ldap_search_sub'] = '如果你想从次级上下文中搜索用户，设值&lt;&gt; 0。';
$string['auth_ldap_update_userinfo'] = '从LDAP向本系统更新用户信息（姓名、地址……）要查看映射信息，请看/auth/ldap/attr_mappings.php';
$string['auth_ldap_user_attribute'] = '用于命名/搜索用户的属性。通常为‘cn’。';
$string['auth_ldapdescription'] = '该方法利用一个外部的LDAP服务器进行身份验证。

                                  如果用户名和密码是有效的，本系统据此在数据库中创建一个新用户。 

                                  该模块可以从LDAP中读取用户属性，并把指定的字段预先填入本系统数据库。 

                                  此后的登录只需检验用户名和密码。';
$string['auth_ldapextrafields'] = '这些字段是可选的。你可以在此指定这些<B>LDAP字段</B>复制到本系统的数据库中。 <P>如果你不选，将使用本系统默认值。<P>无论以上何种情况，用户在登录之后都可以修改这些字段。';
$string['auth_ldaptitle'] = '使用一个LDAP服务器';
$string['auth_manualdescription'] = '该方法不允许用户以任何方式创建帐号。所有帐号只能由管理员手工创建。';
$string['auth_manualtitle'] = '只允许手工添加帐号';
$string['auth_nntpdescription'] = '该方法使用一个NNTP服务器来检验用户名和密码是否有效。';
$string['auth_nntphost'] = 'NNTP服务器地址。用IP地址，不要用域名。';
$string['auth_nntpport'] = '服务器端口（通常是119）';
$string['auth_nntptitle'] = '使用一个NNTP服务器';
$string['auth_nonedescription'] = '用户可以即刻进入本系统并创建一个有效帐号，不需要任何身份验证，也不需要电子邮件确认。慎用该方法——考虑一下安全性和管理上的问题。';
$string['auth_nonetitle'] = '没有身份验证';
$string['auth_pop3description'] = '该方法使用一个POP3服务器来检验用户名和密码。';
$string['auth_pop3host'] = 'POP3服务器地址。用IP地址，不要用域名。';
$string['auth_pop3port'] = '服务器端口（通常是110）';
$string['auth_pop3title'] = '使用一个POP3服务器';
$string['auth_pop3type'] = '服务器类型。如果你的POP3服务器使用安全验证，请选择pop3cert。';
$string['auth_user_create'] = '激活用户创建功能';
$string['auth_user_creation'] = '新的(匿名)用户可以在外部身份验证源中创建新用户帐号，并通过email确认。如果你激活了这个功能，请记住同时也为用户创建功能设置一下模块特定选项';
$string['auth_usernameexists'] = '选中的用户名已经存在。请选择一个新的。';
$string['authenticationoptions'] = '身份验证选项';
$string['authinstructions'] = '你在这里可以给你的用户提供使用说明，让他们知道该用哪个用户名和密码。你在这里输入的文本将出现在登录页面。如果留空不填，登录页面将不会出现使用说明。';
$string['changepassword'] = '更改密码地址（URL）';
$string['changepasswordhelp'] = '在这里你可以指定一个位置用户可以重新获得或更改他们的用户名/密码。这将在登录页面显示一个按钮。如果留空不填，就不会有按钮出现。';
$string['chooseauthmethod'] = '选择一个身份验证方法：';
$string['guestloginbutton'] = '访客登录按钮';
$string['instructions'] = '使用说明';
$string['md5'] = 'MD5加密';
$string['plaintext'] = '纯文本';
$string['showguestlogin'] = '你可以在登录页面显示或隐藏访客登录按钮。';
//  1.2
$string['auth_multiplehosts'] = "Multiple hosts can be specified (eg host1.com;host2.com;host3.com";
?>
