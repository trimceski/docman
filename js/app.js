/*global window, $, FormData, alert, confirm*/

$(function () {
    'use strict';
    var selected;

    $(document).ready(function () {
        //Additional buttons in table header
        $('<button class="btn btn-default edit" type="button" name="edit" title="Edit"><i class="glyphicon glyphicon-edit icon-edit"></i></button><button class="btn btn-default remove" type="button" name="remove" title="Remove"><i class="glyphicon glyphicon-remove icon-remove"></i></button><button class="btn btn-default download" type="button" name="download" title="Download"><i class="glyphicon glyphicon-download-alt icon-download-alt"></i></button>').insertAfter($('.fixed-table-toolbar button').first());

        $(document).on('submit', 'form[name="frm_add"]', function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: 'api/',
                dataType: "json",
                processData: false,
                contentType: false,
                data: new FormData($(this)[0])
            }).done(function (data) {
                if (!data.error) {
                    $('#addModal').modal('toggle');
                    $('table').bootstrapTable('refresh');
                }
            });
        });

        $(document).on('submit', 'form[name="frm_edit"]', function (e) {
            e.preventDefault();
            $.ajax({
                type: "PUT",
                url: 'api/document/' + selected[0].id,
                dataType: "json",
                data: $(this).serialize()
            }).done(function (data) {
                if (!data.error) {
                    $('#editModal').modal('toggle');
                    $('#documents').bootstrapTable('refresh');
                }
            });
        });

        $(document).on('submit', 'form[name="frm_addCat"]', function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: 'api/categories',
                dataType: "json",
                data: $(this).serialize()
            }).done(function (data) {
                if (!data.error) {
                    $('form[name="frm_addCat"]')[0].reset();
                    $('#categories').bootstrapTable('refresh');
                }
            });
        });

        $(document).on('click', '.btn.edit, .btn.download, .btn.remove', function (e) {
            e.preventDefault();
            selected = $('#documents').bootstrapTable('getSelections');
            if (selected.length === 0) {
                alert("Please select an document");
                return;
            }

            if ($(this).hasClass('download')) {
                window.location = 'api/download/' + selected[0].id;
            } else if ($(this).hasClass('edit')) {
                $('#editModal').find('form')[0].reset();
                $('#editModal').modal('show');
                $('#editModal .loading').show();
                $.get('api/categories', function (categories) {
                    var content = '<option value="">Select category</option>',
                        i;

                    for (i = 0; i < categories.rows.length; i += 1) {
                        content += '<option value="' + categories.rows[i].id + '">' + categories.rows[i].title + '</option>';
                    }
                    $('#editModal').find('select').html($(content));
                    $('#editModal input[name="name"]').val(selected[0].name);
                    $('#editModal textarea').val(selected[0].description);
                    $('#editModal select').val(selected[0].category_id);
                    $('#editModal .loading').hide();

                });
            } else if ($(this).hasClass('remove')) {
                if (confirm('Are you sure that you want to delete this document?')) {
                    $.ajax({
                        type: "DELETE",
                        url: 'api/document/' + selected[0].id,
                        dataType: "json"
                    }).done(function (data) {
                        if (!data.error) {
                            $('table').bootstrapTable('refresh');
                        }
                    });
                }
            }
        });

        $('#addModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
        });

        $('#addModal').on('show.bs.modal', function (e) {
            $.get('api/categories', function (categories) {
                var content = '<option value="">Select category</option>',
                    i;

                for (i = 0; i < categories.rows.length; i += 1) {
                    content += '<option value="' + categories.rows[i].id + '">' + categories.rows[i].title + '</option>';
                }

                $('#addModal').find('select').html($(content));
            });
        });

        $('#catModal').on('show.bs.modal', function () {
            $('#categories').bootstrapTable('refresh');
        });
    });
});
