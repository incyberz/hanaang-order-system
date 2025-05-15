<style>
  .blok-last-update {
    display: none;
    position: fixed;
    inset: auto 15px 15px 15px;
    background: yellow;
    padding: 15px;
    border-radius: 10px;
    z-index: 999;
  }
</style>
<?php
echo "
  <div class='blok-last-update'>
    <div class=mb-4>Terjadi perubahan pada Order ini oleh Petugas. <div class='bold darkblue'>Silahkan refresh!</div></div>
    <div class='hideit'>
      <div class=bold>Last Update</div>
      <div class=hideit id='id_order'>$order[id]</div>
      <div id='last_update'>$order[last_update]</div>
    </div>
    <button class='btn btn-primary w-100' onclick='location.reload()'>Refresh</button>
  </div>
";
?>
<script>
  $(function() {
    let id_order = $('#id_order').text();
    let last_update = $('#last_update').text();
    let link_ajax = `ajax/ajax_get_last_update_order.php?last_update=${last_update}&id_order=${id_order}`;

    let cekLastUpdate = setInterval(() => {
      $.ajax({
        url: link_ajax,
        success: function(a) {
          if (parseInt(a)) {
            $('.blok-last-update').fadeIn();
            clearInterval(cekLastUpdate);
          }
        }
      })
    }, 3000);
  })
</script>