<?PHP // $Id$ 
      // auth.php - created with Moodle 1.3.1 (2004052501)


$string['auth_dbdescription'] = 'Cara ini menggunakan pangkalan data luaran untuk memeriksa samada nama pengguna dan kata laluan yang dimasukkan adalah sah. Jika akaun adalah akaun baru, maka maklumat dari jadual-jadual yang lain juga boleh dimasukkan ke Moodle.';
$string['auth_dbextrafields'] = 'Ruang-ruang ini adalah pilihan. Anda dapat memilih untuk memasukkan lebih dulu beberapa ruang pengguna dari Moodle dengan maklumat dari <B>ruangan pangkalan data luaran</B> yang anda tentukan disini. <P>Jika anda kosongkan, maka maklumat asal yang akan digunakan.<P>Walau bagaimana pun, pengguna akan dapat mengubah kesemua ruangan ini setelah mereka daftar masuk.';
$string['auth_dbfieldpass'] = 'Nama dari ruangan yang berisi kata laluan';
$string['auth_dbfielduser'] = 'Nama dari ruangan yang berisi nama pengguna';
$string['auth_dbhost'] = 'Komputer yang menyimpan pelayan pangkalan data.';
$string['auth_dbname'] = 'Nama pangkalan data itu sendiri';
$string['auth_dbpass'] = 'Kata laluan sama dengan nama pengguna';
$string['auth_dbpasstype'] = 'Terangkan format kata laluan itu disimpan. Enkripsi MD5 sangat berguna untuk tujuan penyambungan dengan aplikasi internet yang lain seperti PostNuke';
$string['auth_dbtable'] = 'Nama jadual pada pangkalan data';
$string['auth_dbtitle'] = 'Gunakan pangkalan data luaran';
$string['auth_dbtype'] = 'Jenis pangkalan data (Lihat <A HREF=../lib/adodb/readme.htm#drivers>Dokumentasi ADOdb</A> untuk keterangannya)';
$string['auth_dbuser'] = 'Nama pengguna yang mempunyai akses baca untuk pangkalan data';
$string['auth_emaildescription'] = 'Pengesahan melalui email adalah cara pendaftaran yang biasa. Apabila pengguna mendaftar, memilih nama pengguna baru dan kata laluan mereka sendiri, sebuah email pengesahan akan dikirim ke alamat email pengguna. Email ini megandungi pautan yang selamat untuk ke halaman dimana pengguna dapat mengesahkan keanggotaannya. Daftar masuk yang berikutnya hanya memeriksa nama pengguna dan password yang tersimpan pada pangkalan data Moodle.';
$string['auth_emailtitle'] = 'Pendaftaran berdasarkan Email';
$string['auth_imapdescription'] = 'Cara ini menggunakan pelayan IMAP untuk memeriksa samada nama pengguna dan kata laluan sah.';
$string['auth_imaphost'] = 'Alamat pelayan IMAP. Gunakan nombor IP, bukan nama DNS.';
$string['auth_imapport'] = 'Nombor port pelayan IMAP. Biasanya ialah 143 atau 993.';
$string['auth_imaptitle'] = 'Gunakan pelayan IMAP';
$string['auth_imaptype'] = 'Jenis pelayan IMAP. Pelayan IMAP mempunyai pelbagai jenis pengesahan dan perundingan.';
$string['auth_ldap_bind_dn'] = 'Jika Anda ingin menggunakan \"bind-user\" untuk mencari pengguna, tentukanlah disini. Misalnya \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Kata laulan untuk ';
$string['auth_ldap_contexts'] = 'Senarai konteks dimana pengguna terletak. Asingkan konteks-konteks yang berbeza dengan \';\'. Sebagai contoh: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'Jika anda membenarkan pendaftaran pengguna menggunakan email, perincikan konteks dimana pengguna didaftarkan. Ini mestilah berbeza daripada pengguna yang lain untuk mengelakkan isu-isu keselamatan. Anda tidak perlu menambahkan konteks ini pada ldap_context-variable, kerana Moodle akan mencari pengguna-pengguna konteks ini secara automatik.';
$string['auth_ldap_creators'] = 'Senarai kumpulan yang mana anggota-anggotanya dibenarkan mencipta kursus-kursus baru. Asingkan kumpulan berbeza degan \";\". Selalunya seperti \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Tentukan hos LDAP dengan format URL  seperti \'ldap://ldap.myorg.com/\' or \'ldaps://ldap.myorg.com/\' ';
$string['auth_ldap_memberattribute'] = 'Perincikan atribut pengguna, apabila pengguna adalah anggota sesebuah kumpulan. Selalunya \'member\'';
$string['auth_ldap_search_sub'] = 'Masukkan nilai <> 0 jika anda ingin untuk mencari pengguna dari sub-konteks.';
$string['auth_ldap_update_userinfo'] = 'Kemaskini maklumat pengguna (nama depan, namaakhir, alamat..) dari LDAP ke Moodle. Lihat /auth/ldap/attr_mappings.php untuk maklumat pemetaannya';
$string['auth_ldap_user_attribute'] = 'Atribut yang digunakan untuk nama/cari pengguna. Biasanya \'cn\'.';
$string['auth_ldap_version'] = 'Versi protokol LDAP pada server ini.';
$string['auth_ldapdescription'] = 'Cara ini melakukan pengesahan melalui pelayan LDAP luaran.
Jika nama pengguna dan password kata laluan adalah sah, Moodle akan mencipta pengguna baru 
dalam pangkalan datanyaa. Modul ini dapat membaca atribut pengguna dari LDAP dan memasukkan terlebih dahulu dalam ruangan yang diperlukan oleh Moodle.  Untuk daftar masuk selanjutnya hanya nama pengguna dan kata laluan yang diperiksa.';
$string['auth_ldapextrafields'] = 'Ruang-ruang ini adalah pilihan. Anda dapat memilih untuk memasukkan terlebih dahulu beberapa ruang pengguna dari Moodle dengan maklumat dari <B>ruangan LDAP</B> yang anda tentukan disini. <P>Jika anda kosongkan, maka tiada maklumat yang akan digunakan.<P>Walau bagaimana pun, pengguna akan dapat mengubah kesemua ruangan ini setelah mereka daftar masuk.';
$string['auth_ldaptitle'] = 'Gunakan pelayan LDAP';
$string['auth_manualdescription'] = 'Cara ini tidak membenarkan pengguna mandaftarkan diri mereka sendiri. Semua akaun mestilah dicipta oleh penyelenggara.';
$string['auth_manualtitle'] = 'Akaun manual sahaja';
$string['auth_multiplehosts'] = 'Pelbagai host boleh dinyatakan (cthnya host1.com;host2.com;host3.com)';
$string['auth_nntpdescription'] = 'Cara ini menggunakan server NNTP untuk memeriksa samada nama pengguna dan kata laluan sah.';
$string['auth_nntphost'] = 'Alamat pelayan NNTP. Gunakan nombor IP, bukan nama DNS.';
$string['auth_nntpport'] = 'Port pelayan  (119 adalah pilihan terbaik)';
$string['auth_nntptitle'] = 'Gunakan pelayan NNTP';
$string['auth_nonedescription'] = 'Pengguna dapat mendaftar dan membuat keanggotaan yang sah segera, tanpa pengesahan pada pelayan luaran dan tidak ada pengesahan melalui email.  Berhati-hatilah jika anda menggunakan pilihan ini - fikirkan mengenai keselamatan dan masalah penyelengaraan yang boleh disebabkan oleh ini.';
$string['auth_nonetitle'] = 'Tidak ada pengesahan';
$string['auth_pop3description'] = 'cara ini menggunakan pelayan POP3 untuk memeriksa apakah nama pengguna dan kata laluan yang dimasukkan sah.';
$string['auth_pop3host'] = 'Alamat pelayan POP3. Gunakan nombor IP, bukan nama DNS.';
$string['auth_pop3port'] = 'Port Server  (110 adalah yang terbaik)';
$string['auth_pop3title'] = 'Gunakan pelayan POP3';
$string['auth_pop3type'] = 'Jenis pelayan. Jika pelayan anda menggunakan sijil keselamatan, pilih pop3cert.';
$string['auth_user_create'] = 'Membolehkan penciptaan pengguna';
$string['auth_user_creation'] = 'Pengguna baru (yang tidak bernama) boleh mendaftarkan akaun diatas sumber pengesahan luaran dan disahkan melalui email. Jika anda membenarkan fungsi ini, anda juga perlu menetapkan pilihan modul spesifik untuk penciptaan pengguna.';
$string['auth_usernameexists'] = 'Nama pengguna itu telah wujud dalam sistem. Sila pilih nama lain.';
$string['authenticationoptions'] = 'Pilihan pengesahan';
$string['authinstructions'] = 'Disini Anda dapat memberikan arahan untuk pengguna anda, agar mereka tahu nama pengguna dan kata laluan mana yang akan mereka perlu gunakan.  Teks yang Anda masukkan akan dipaparkan pada halaman daftar masuk.  Jika Anda mengosongkan ini maka tidak ada arahan yang akan dipaparkan.';
$string['changepassword'] = 'URL pengubahan kata laluan';
$string['changepasswordhelp'] = 'Disini Anda dapat menentukan lokasi dimana pengguna Anda dapat memperbaiki atau mengganti nama pengguna/password jika mereka lupa.  Ini akan disediakan untuk pengguna melalui butang pada halaman daftar masuk.  Jika Anda membiarkan kosong, maka butang tersebut tidak akan ditampilkan.';
$string['chooseauthmethod'] = 'Pilih cara pengesahan:';
$string['guestloginbutton'] = 'Butang daftar masuk Tetamu';
$string['instructions'] = 'Arahan';
$string['md5'] = 'Enkripsi MD5';
$string['plaintext'] = 'Teks biasa';
$string['showguestlogin'] = 'Anda dapat menyembunyikan atau memaparkan butang daftar masuk tamu pada halaman daftar masuk.';

?>
