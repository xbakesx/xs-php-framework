
function updateTable() {
    
    var tbody = $('#scaffoldingTable tbody');
    tbody.empty();
    
    for (var row in scaffoldingTableData) {
        var tr = $(document.createElement('tr'));
        
        for (var col in scaffoldingTableColumns) {
            var column = scaffoldingTableColumns[col];
            
            var td = $(document.createElement('td'));
            
            var text = scaffoldingTableData[row][column];
            if (scaffoldingTableOptions[column]) {
                text = scaffoldingTableOptions[column][text];
            }
            td.text(text);
            
            tr.append(td);
        }

        var td = $(document.createElement('td'));
        
        td.append(createEditButton(scaffoldingTableData[row]));
        td.append(' - ');
        td.append(createDeleteButton(scaffoldingTableData[row]));
        
        tr.append(td);
        
        tbody.append(tr);
    }
}

function createEditButton(data) {
    var btn =  createButton('Edit', data, scaffoldingEditClicked);
    btn.attr('data-toggle', 'modal');
    return btn;
}

function createDeleteButton(data) {
    var btn = createButton('Delete', data, scaffoldingDeleteClicked);
    btn.addClass('btn-danger');
    return btn;
}

function createButton(text, data, onClick) {
    var a = $(document.createElement('a'));
    a.attr('href', '#');
    a.addClass('btn').addClass('btn-primary').addClass('btn-small');
    a.text(text);
    a.bind('click', function( evt ) {
        
        evt.preventDefault();
        
        if ($(this).attr('disabled')) {
            return false;
        }
        return onClick(data);
    });
    return a;
}

function scaffoldingEditClicked(data) {
    scaffoldingUpdateDialogTitle('Edit');
    data.action = 'update';
    scaffoldingPopulateDialogForm(data);
    $('#addEditDialog').modal('show');
}

function scaffoldingDeleteClicked(data) {
    if (confirm('Are you sure?')) {
        data.model = model;
        post('/scaffolding/delete', data, scaffoldingSuccess, function() {});
    }
}

function scaffoldingAddClicked() {
    if ($(this).attr('disabled')) {
        return false;
    }
    scaffoldingUpdateDialogTitle('Add');
    scaffoldingPopulateDialogForm({'action': 'create'});
    $('#addEditDialog').modal('show');
}

function scaffoldingSuccess(newData) {
    if (newData.error) {
        handleScaffoldingError(newData.error);
    } else {
        scaffoldingTableData = newData.data;
        if (newData.options) {
            scaffoldingTableOptions = newData.options;
        } else {
            scaffoldingTableOptions = {};
        }
        updateTable();
        $('#addEditDialog').modal('hide');
    }
}

function scaffoldingUpdateDialogTitle(title) {
    $('#addEditDialog .modal-header h3').text(title);
}

function handleScaffoldingError(errorMessage) {
    alert(errorMessage);
}

function scaffoldingPopulateDialogForm(data) {
    var form = $('#addEditDialog form');
    form.find('select').val([]);
    form.find('input[type!="hidden"]').val('');
    for (var i in data) {
        $(document.getElementById(i)).val(data[i]);
        $(document.getElementById('data_' + i)).val(data[i]);
    }
}

$(document).ready(function() {
    $(document.getElementById('scaffoldingAddButton')).bind('click', scaffoldingAddClicked);
    var btn = $(document.getElementById('submit'));
    btn.bind('click', function( evt ) {
        evt.preventDefault();
        var arrays = $('#addEditDialog form').serializeArray();
        var data = {};
        for (var i in arrays) {
            data[arrays[i]['name']] = arrays[i]['value'];
        }
        post('/scaffolding/update', data, scaffoldingSuccess, function (data) {
            
        });
    });
    updateTable();
});