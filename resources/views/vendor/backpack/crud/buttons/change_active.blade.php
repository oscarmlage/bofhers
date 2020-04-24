<a onclick="change_active(this)" class="btn btn-default btn-sm bulk-button"><i class="fa fa-check"></i> Change Active</a>

@push('after_scripts')
<script>
function change_active(button) {
    $.ajax({
        type: 'POST',
        url: "{{ route('quote.change_active') }}",
        data: { items: crud.checkedItems },
        success:function(data){
            crud.checkedItems = [];
            crud.table.ajax.reload();
        }
    });
}
</script>
@endpush
