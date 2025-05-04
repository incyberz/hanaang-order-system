<?php
echo "<div class='my-3 text-success'>Login success...</div>


  <hr>
  <div class='alert alert-danger'>Nomor Whatsapp Anda belum terverifikasi. Silahkan hubungi Admin!</div>
";

$nama_nospace = str_replace(' ', '-', $d['nama']);
$link_verif = "$nama_server?verifikasi_whatsapp&nama=$nama_nospace&username=$d[username]&whatsapp=$d[whatsapp]";

$text_asal = "```================================\nREQUEST VERIFIKASI AKUN\nfrom: $d[whatsapp] | UNCHECKED!\n================================```\n\nYth. Petugas Admin Hanaang ($petugas_default[nama]),\n\nMohon verifikasi akun saya atas nama:\n- *nama:* $d[nama]\n- *username:* $d[username] \n\nTerimakasih.\n\nLink:\n$link_verif$text_wa_from";
$preview = str_replace("\n\n", '<br>.<br>', $text_asal);
$preview = str_replace("\n", '<br>', $preview);
$preview = str_replace('```', '', $preview);

$text_wa = urlencode($text_asal);

$link_wa = "$https_api_wa?phone=$petugas_default[whatsapp]&text=$text_wa";

echo "
  <div class='card p-2'>
    <ul>
      <li>
        <b>Nama:</b> $d[nama]
      </li>
      <li>
        <b>Username:</b> $d[username]
      </li>
      <li>
        <b>Whatsapp Status:</b> <i style='color:red'>unverified</i>
      </li>
    </ul>
    <div class='card p-2 wa_preview' >$preview</div>
    <a target=_blank href='$link_wa' class='btn btn-primary w-100 mt-2'>Hubungi Whatsapp Admin</a>
  </div>
  <div class='mt-2 text-center'><a href='?logout' onclick='return confirm(`Logout?`)'>Logout</a></div>

";
