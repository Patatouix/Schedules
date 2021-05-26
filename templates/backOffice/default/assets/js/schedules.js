$(function () {

    // create a default schedule
    $('a.schedules-default').on('click', function(e) {
        $(".period").addClass("hidden");
        $("#attr-schedules-closed").val(0);
        $("#attr-schedules-period-begin input, #attr-schedules-period-end input").prop('required', false);
        $("#attr-schedules-day").prop('required', true);
        $('#attr-schedules-day option').prop('selected', false);
    });

    // create an extra schedule
    $('a.schedules-extra').on('click', function(e) {
        $(".period").removeClass("hidden");
        $("#attr-schedules-closed").val(0);
        $("#attr-schedules-period-begin input, #attr-schedules-period-end input").prop('required', true);
        $("#attr-schedules-day").prop('required', false);
        $('#attr-schedules-day option').prop('selected', false);
    });

    // create a closure schedule
    $('a.schedules-closed').on('click', function(e) {
        $(".period").removeClass("hidden");
        $("#attr-schedules-closed").val(1);
        $("#attr-schedules-period-begin input, #attr-schedules-period-end input").prop('required', true);
        $("#attr-schedules-day").prop('required', false);
        $('#attr-schedules-day option').prop('selected', false);
    });

    // delete a schedule
    $('a.schedules-delete').on('click', function(e) {
        $('#schedules_delete_schedule_id').val($(this).data('schedule-id'));
        $("#attr-schedules-period-begin input, #attr-schedules-period-end input").prop('required', false);
        $("#attr-schedules-day").prop('required', false);
    });

    // update a default schedule
    $('a.schedules-update-default').on('click', function(e) {
        $(".period").addClass("hidden");
        $("#attr-schedules-schedule-id").val($(this).data('schedule-id'));
        $("#attr-schedules-begin input").val($(this).data('begin'));
        $("#attr-schedules-end input").val($(this).data('end'));
        $("#attr-schedules-stock input").val($(this).data('stock'));
        $('#attr-schedules-day option[value="' + $(this).data('day') + '"]').prop('selected', true);
        $("#attr-schedules-period-begin input").val("");
        $("#attr-schedules-period-end input").val("");
        $("#update-attr-schedules-closed").val(0);
        $("#attr-schedules-period-begin input, #attr-schedules-period-end input").prop('required', false);
        $("#attr-schedules-day").prop('required', true);
    });

    // update an extra schedule
    $('a.schedules-update-extra').on('click', function(e) {
        $(".period").removeClass("hidden");
        $("#attr-schedules-schedule-id").val($(this).data('schedule-id'));
        $("#attr-schedules-begin input").val($(this).data('begin'));
        $("#attr-schedules-end input").val($(this).data('end'));
        $("#attr-schedules-period-begin input").val($(this).data('periodbegin'));
        $("#attr-schedules-period-end input").val($(this).data('periodend'));
        $("#attr-schedules-stock input").val($(this).data('stock'));
        $('#attr-schedules-day option[value="' + $(this).data('day') + '"]').prop('selected', true);
        $("#update-attr-schedules-closed").val(0);
        $("#attr-schedules-period-begin input, #attr-schedules-period-end input").prop('required', true);
        $("#attr-schedules-day").prop('required', false);
    });

    // update a closure schedule
    $('a.schedules-update-closed').on('click', function(e) {
        $(".period").removeClass("hidden");
        $("#attr-schedules-schedule-id").val($(this).data('schedule-id'));
        $("#attr-schedules-begin input").val($(this).data('begin'));
        $("#attr-schedules-end input").val($(this).data('end'));
        $("#attr-schedules-period-begin input").val($(this).data('periodbegin'));
        $("#attr-schedules-period-end input").val($(this).data('periodend'));
        $("#attr-schedules-stock input").val($(this).data('stock'));
        $('#attr-schedules-day option[value="' + $(this).data('day') + '"]').prop('selected', true);
        $("#update-attr-schedules-closed").val(1);
        $("#attr-schedules-period-begin input, #attr-schedules-period-end input").prop('required', true);
        $("#attr-schedules-day").prop('required', false);
    });

    // clone a schedule
    $('a.schedules-clone').on('click', function(e) {
        $("#attr-schedules-clone-schedule-id", "#schedules-clone").val($(this).data('schedule-id'));
        $("#attr-schedules-stock input").val($(this).data('stock'));
        $("#attr-schedules-period-begin input, #attr-schedules-period-end input").prop('required', false);
        $("#attr-schedules-day").prop('required', false);
    });
});