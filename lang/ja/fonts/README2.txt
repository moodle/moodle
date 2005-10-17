README.txt に書いてある方法でもOKですが，
sazanami フォントだと，活動レポートでのグラフの横軸の文字列が
ちょっと見にくいようです。

このディレクトリに置く default.ttf としては，
独立行政法人 情報処理推進機構のフォント(IPAフォント)付き GRASS国際化版(i18n)
http://www.grass-japan.org/FOSS4G/readme-grass-i18n-ipafonts.eucjp.htm
に同封されている ipag.ttf を名前を変えて使うと良いでしょう。
# ipag.ttf のみの配布は行われていませんので，GRASS国際化版(i18n) を
# ダウンロードし，抜き出す必要があります。
# GRASS国際化版(i18n) 以外のソフトでの ipag.ttf の使用は許されています。

上記ページより，
grass5.0.3_i686-pc-linux-i18n-ipa1-gnu_bin.tar.gz
をダウンロードし，
UNIX系のシステムでは，
tar xvzf grass5.0.3_i686-pc-linux-i18n-ipa1-gnu_bin.tar.gz
で展開してください。
# Windows では，Lhaca デラックス版
# http://park8.wakwak.com/~app/Lhaca/
# http://www.vector.co.jp/soft/win95/util/se166893.html
# などを用いると展開できます。

展開してできた fonts フォルダの中にある ipag.ttf を
default.ttf と名前を変えて，
このディレクトリにコピーしてください。

-- 喜多敏博  http://t-kita.net
