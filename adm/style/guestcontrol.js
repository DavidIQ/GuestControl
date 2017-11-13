$(function() {
    $('input[name=all_forums]').change(function() {
        $gc_forums = $('#gc_forums');
        if (this.checked)
        {
            $gc_forums.find('option:selected').prop('selected', false);
            $gc_forums.prop('disabled', true);
        }
        else
        {
            $gc_forums.prop('disabled', false);
        }
    });
});
