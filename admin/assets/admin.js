// Generic AJAX delete handler
$(document).on('click', '.btn-delete', function(e){
  e.preventDefault();
  if(!confirm('Delete this item? This cannot be undone.')) return;
  const $btn = $(this);
  const id = $btn.data('id');
  const type = $btn.data('type');
  $.post('ajax/delete.php', { id: id, type: type }, function(res){
    if(res.success){
      $btn.closest('tr').fadeOut(200, function(){ $(this).remove(); });
    } else {
      alert(res.message || 'Failed to delete');
    }
  }, 'json').fail(function(){ alert('Request failed'); });
});
