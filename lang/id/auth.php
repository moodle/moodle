<?PHP // $Id$ 
      // auth.php - created with Moodle 1.2.1 (2004032500)


$string['auth_dbdescription'] = 'Metode ini menggunakan tabel database eksternal untuk memeriksa apakah nama pengguna dan password yang dimasukkan adalah sah. Jika keanggotaan adalah keanggotaan baru, maka informasi dari field-field yang lain juga bisa dikopi ke Moodle.';
$string['auth_dbextrafields'] = 'Field-field ini adalah pilihan. Anda dapat memilih untuk memasukkan lebih dulu beberapa field-field pengguna dari Moodle dengan informasi dari <B>field-field database eksternal</B> yang Anda tentukan disini. <P>Jika Anda kosongkan, maka aturan default yang akan digunakan.<P>Pada kasus lainnya, pengguna akan dapat mengedit semua field-field ini setelah mereka login.';
$string['auth_dbfieldpass'] = 'Nama dari field yang berisi password';
$string['auth_dbfielduser'] = 'Nama dari field yang berisi nama pengguna';
$string['auth_dbhost'] = 'Komputer yang meng-host server database.';
$string['auth_dbname'] = 'Nama database itu sendiri';
$string['auth_dbpass'] = 'Password sama dengan nama pengguna di atas';
$string['auth_dbpasstype'] = 'Tentukan format yang akan digunakan oleh field password. Enkripsi MD5 sangat berguna untuk berhubungan dengan aplikasi web lainnya seperti PostNuke';
$string['auth_dbtable'] = 'Nama dari tabel pada database';
$string['auth_dbtitle'] = 'Gunakan database eksternal';
$string['auth_dbtype'] = 'Jenis database (Lihat <A HREF=../lib/adodb/readme.htm#drivers>Dokumentasi ADOdb</A> untuk penjelasannya)';
$string['auth_dbuser'] = 'Nama pengguna yang mempunyai akses pembacaan ke database';
$string['auth_emaildescription'] = 'Konfirmasi via email adalah metode otentikasi default. Saat pengguna mendaftar, memilih nama pengguna baru dan password mereka sendiri, sebuah email konfirmasi akan dikirim ke alamat email pengguna. Email ini berisi link yang aman untuk ke halaman dimana pengguna dapat mengkonfirmasi keanggotaannya. Login berikutnya hanya memeriksa nama pengguna dan password yang tersimpan pada databse Moodle.';
$string['auth_emailtitle'] = 'Otentikasi berdasarkan Email';
$string['auth_imapdescription'] = 'Metode ini menggunakan server IMAP untuk memeriksa apakah nama pengguna dan password sah.';
$string['auth_imaphost'] = 'Alamat server IMAP. Gunakan nomor IP, bukan nama DNS.';
$string['auth_imapport'] = 'Nomor port server IMAP. Biasanya dipakai 143 atau 993.';
$string['auth_imaptitle'] = 'Gunakan server IMAP';
$string['auth_imaptype'] = 'Jenis server IMAP. Server IMAP dapat mempunyai berbagai jenis otentikasi dan negosiasi.';
$string['auth_ldap_bind_dn'] = 'Jika Anda ingin menggunakan bind-user untuk mencari pengguna, tentukanlah disini. Misalnya \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Password untuk bind-user.';
$string['auth_ldap_contexts'] = 'Daftar dari konteks dimana pengguna dilokasikan. Pisahkan konteks lainnya dengan \';\'. Sebagai contoh: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Jika Anda mengaktifkan pembuatan pengguna dengan konfirmasi email, tentukan keadaan bagaimana pengguna akan dibuat. Keadaan ini harus berbeda dengan pengguna lainnya untuk menanggulangi bahaya keamanan. Anda tidak perlu menambahkan keadaan ini pada pemakaian variabel ldap_context, Moodle akan mencari pengguna secara otomatis dari keadaan ini.';
$string['auth_ldap_creators'] = 'daftar grup dari anggota yang diperbolehkan untuk membuat kursus baru. Pisahkan grup-grup dengan \';\'. Biasanya sesuatu seperti \'cn=guru,ou=staf,o=orgsaya\'';
$string['auth_ldap_host_url'] = 'Tentukan host LDAP pada form-URL  seperti \'ldap://ldap.myorg.com/\' or \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Tentukan atribut keanggotaan anggota, jika pengguna adalah anggota grup. Biasanya \'anggota\'';
$string['auth_ldap_search_sub'] = 'Masukkan nilai &lt;&gt; 0 jika Anda ingin untuk mencari pengguna dari sub-konteks.';
$string['auth_ldap_update_userinfo'] = 'Perbaharui informasi pengguna (nama depan, namaakhir, alamat..) dari LDAP ke Moodle. Lihat pada /auth/ldap/attr_mappings.php untuk informasi pemetaannya';
$string['auth_ldap_user_attribute'] = 'Attribut yang dugunakan untuk nama/cari pengguna. Biasanya \'cn\'.';
$string['auth_ldapdescription'] = 'Metode ini melakukan otentikasi melalui server LDAP eksternal.
                                  Jika nama pengguna dan password yang dimasukkan adalah sah, Moodle akan membuat pengguna baru 
                                  dimasukkan pada databasenya. Modul ini dapat membaca atribut pengguna dari LDAP dan memasukkan lebih dulu 
                                  field-field yang dibutuhkan pada Moodle.  Untuk login selanjutnya hanya nama pengguna dan 
                                  password yang diperiksa.';
$string['auth_ldapextrafields'] = 'Field-field ini adalah pilihan.  Anda dapat memilih untuk memasukkan lebih dulu beberapa field-field pengguna dari Moodle dengan informasi dari <B>field-field LDAP</B> yang Anda tentukan disini. <P>Jika Anda mengosongkan field-field ini, maka tidak ada yang ditransfer dari LDAP dan default Moodle yang akan digunakan.<P>Pada kasus lain, pengguna akan dapat mengedit semua field-field ini setelah mereka login.';
$string['auth_ldaptitle'] = 'Gunakan server LDAP';
$string['auth_manualdescription'] = 'Metode ini menghilangkan segala cara untuk seorang pengguna untuk membuat keanggotaan mereka sendiri. Semua keanggotaan harus secara manual dibuat oleh pengguna Admin.';
$string['auth_manualtitle'] = 'Keanggotaan hanya secara manual';
$string['auth_multiplehosts'] = 'Multi host bisa ditentukan dengan cara (mis.host1.com;host2.com;host3.com';
$string['auth_nntpdescription'] = 'Metode ini menggunakan server NNTP untuk memeriksa apakah nama pengguna dan password sah.';
$string['auth_nntphost'] = 'Alamat server NNTP. Gunakan nomor IP, bukan nama DNS.';
$string['auth_nntpport'] = 'Port Server  (119 adalah pilihan terbaik)';
$string['auth_nntptitle'] = 'Gunakan server NNTP';
$string['auth_nonedescription'] = 'Pengguna dapat mendaftar dan membuat keanggotaan yang sah segera, tanpa otentikasi pada server eksternal dan tidak ada konfirmasi melalui email.  Berhati-hatilah untuk menggunakan pilihan ini - pikirkan mengenai keamanan dan problem administrasi yang dapat disebabkan oleh ini.';
$string['auth_nonetitle'] = 'Tidak ada otentikasi';
$string['auth_pop3description'] = 'Metode ini menggunakan server POP3 untuk memeriksa apakah nama pengguna dan password yang dimasukkan sah.';
$string['auth_pop3host'] = 'Alamat server POP3. Gunakan nomor IP, bukan nama DNS.';
$string['auth_pop3port'] = 'Port Server  (110 adalah yang terbaik)';
$string['auth_pop3title'] = 'Gunakan server POP3';
$string['auth_pop3type'] = 'Jenis server. Jika server Anda menggunakan sertifikat keamanan, pilih pop3cert.';
$string['auth_user_create'] = 'Aktifkan pembuatan oleh pengguna';
$string['auth_user_creation'] = 'Pengguna anonymous baru dapat membuat keanggotaannya dari luar sistem dan mengkonfirmasikan via email. Jika Anda mengaktifkan ini, ingatlah untuk juga melakukan pengaturan pilihan modul khusus untuk pembuatan keanggotaan.';
$string['auth_usernameexists'] = 'Username yang dipilih sudah terpakai. Silahkan memilih yang lain.';
$string['authenticationoptions'] = 'Pilihan Otentikasi';
$string['authinstructions'] = 'Disini Anda dapat menyediakan instruksi untuk pengguna Anda, agar mereka tahu nama pengguna dan password mana yang akan mereka gunakan.  Teks yang Anda masukkan akan ditampilkan pada halaman login.  Jika Anda mengosongkan ini maka tidak ada instruksi yang akan ditampilkan.';
$string['changepassword'] = 'Ubah password URL';
$string['changepasswordhelp'] = 'Disini Anda dapat menentukan lokasi dimana pengguna Anda dapat memperbaiki atau mengganti nama pengguna/password jika mereka lupa.  Ini akan disediakan untuk pengguna melalui tombol pada halaman login.  Jika Anda membiarkan kosong, maka tombol tersebut tidak akan ditampilkan.';
$string['chooseauthmethod'] = 'Pilih metode otentikasi:';
$string['guestloginbutton'] = 'Tombol Login Tamu';
$string['instructions'] = 'Instruksi';
$string['md5'] = 'Enkripsi MD5';
$string['plaintext'] = 'Plain Teks';
$string['showguestlogin'] = 'Anda dapat menyembunyikan atau menampilkan tombol login tamu pada halaman login.';

?>
