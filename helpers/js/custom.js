var AbsUrl = $("body").attr("data-absurl");
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
var UTYPE = $("body").attr("data-usrType");

function openNewMessageModal() {
    document.getElementById('newMessageArea').style.display = 'block';
    document.getElementById('conversationArea').style.display = 'none';
    document.getElementById('replyArea').style.display = 'none';
}

function sendNewMessage() {
    const messageText = document.getElementById('newMessageText').value;

    if (!messageText.trim()) {
        alert('Message cannot be empty.');
        return;
    }
    fetch(AbsUrl + 'user/send-message/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            receiver_id: 1,
            message_text: messageText,
            reply_to: null
        })
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert('Message sent successfully!');
                document.getElementById('newMessageText').value = '';
                document.getElementById('messageModal').classList.remove('show');
                document.body.classList.remove('modal-open');
                document.querySelector('.modal-backdrop').remove();
            } else {
                alert('Failed to send message.');
            }
        });
}

$(document).ready(function () {
    $.ajaxSetup({
        error: function (jqXHR, exception, err) {
            if (exception === 'parsererror') {
                $('#ErrMsg').html('<div class="alert alert-warning alert-dismissable">Sorry, Requested JSON parse failed<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>').fadeIn('slow');
            } else if (exception === 'timeout') {
                $('#ErrMsg').html('<div class="alert alert-warning alert-dismissable">Sorry, Server took too long time to respond<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>').fadeIn('slow');
            } else if (exception === 'abort') {
                $('#ErrMsg').html('<div class="alert alert-warning alert-dismissable">Sorry, AJAX request cancelled<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>').fadeIn('slow');
            } else {
                $('#ErrMsg').html('<div class="alert alert-warning alert-dismissable">' + jqXHR.responseText + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>').fadeIn('slow');
            }
            console.log(jqXHR.responseText);
            console.log(exception);
            console.log(err);
            return false;
        }
    });
    if ($('#hotelTbl').length > 0) {
        var src = $('#hotelTbl').attr('data-get-ajax');
        $('#hotelTbl').dataTable({
            "destroy": true,
            "async": true,
            "processing": true,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "ajax": {
                "url": src,
                "dataSrc": function (json) {
                    return json;
                }
            }
        });
    }
    
    if ($('#addTrip').length > 0) {
        let childCount = 0;
        $('.add-child').click(function () {
            childCount++;
            let childField = `
            <div class="col-md-3 child-age-field mt-2" id="child-${childCount}">
                <label><strong>Child ${childCount} Age:</strong></label>
                <input type="number" class="form-control mb-2" name="child_age[]" placeholder="Enter age of child ${childCount}" required="required" min="1" max="11">
                <div class="invalid-feedback">Please Enter age of Child</div>
                <div class="valid-feedback">Looks Good!</div>
                <button class="btn btn-danger remove-child" type="button"><i class="fa-solid fa-minus"></i></button>
            </div>
        `;
            $('#children-ages').append(childField);
        });
        $('#children-ages').on('click', '.remove-child', function () {
            $(this).closest('.child-age-field').remove();
        });
        let contactCount = 1;
        $('.add-contact').click(function () {
            contactCount++;
            let contactField = `
            <div class="col-md-3 contact-field mt-2" id="contact-${contactCount}">
                <label><strong>Phone Number ${contactCount}:</strong></label>
                <input type="tel" class="form-control mb-2" name="phn_num[]" placeholder="Enter Phone Number ${contactCount}" required="required">
                <div class="invalid-feedback">Please Enter Phone Number</div>
                <div class="valid-feedback">Looks Good!</div>
                <button class="btn btn-danger remove-contact" type="button"><i class="fa-solid fa-minus"></i></button>
            </div>
        `;
            $('#add-contacts').append(contactField);
        });
        $('#add-contacts').on('click', '.remove-contact', function () {
            $(this).closest('.contact-field').remove();
        });
        $('#night').on('input', function () {
            let nights = parseInt($(this).val());
            let days = nights + 1;
            $('#numNight').text(`${nights} Night${nights > 1 ? 's' : ''}, ${days} Day${days > 1 ? 's' : ''}`);
        });
        $('#salesTeam').select2({
            placeholder: "Select a Sales Team",
            allowClear: true,
            theme: "bootstrap-5"
        });
        $('#dest').select2({
            placeholder: "Select a Destination",
            allowClear: true,
            theme: "bootstrap-5"
        });
    }

    if ($('#usrTbl').length > 0) {
        var src = $('#usrTbl').attr('data-get-ajax');
        $('#usrTbl').dataTable({
            "destroy": true,
            "async": true,
            "processing": true,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "columns": [
                null,
                null,
                null,
                null,
                null,
                null,
                {"width": "262px"}
            ],
            "ajax": {
                "url": src,
                "dataSrc": function (json) {
                    return json;
                }
            }
        });
    }

    if ($('#verTbl').length > 0) {
        var src = $('#verTbl').attr('data-get-ajax');
        $('#verTbl').dataTable({
            "destroy": true,
            "async": true,
            "processing": true,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "ajax": {
                "url": src,
                "dataSrc": function (json) {
                    return json;
                }
            }
        });
    }

    if ($('#vehicleTbl').length > 0) {
        var src = $('#vehicleTbl').attr('data-get-ajax');
        $('#vehicleTbl').dataTable({
            "destroy": true,
            "async": true,
            "processing": true,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "ajax": {
                "url": src,
                "dataSrc": function (json) {
                    return json;
                }
            }
        });
    }

    if ($('#driverTbl').length > 0) {
        var src = $('#driverTbl').attr('data-get-ajax');
        $('#driverTbl').dataTable({
            "destroy": true,
            "async": true,
            "processing": true,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "ajax": {
                "url": src,
                "dataSrc": function (json) {
                    return json;
                }
            }
        });
    }

    if ($('#agentTbl').length > 0) {
        var src = $('#agentTbl').attr('data-get-ajax');
        $('#agentTbl').dataTable({
            "destroy": true,
            "async": true,
            "processing": true,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "ajax": {
                "url": src,
                "dataSrc": function (json) {
                    return json;
                }
            }
        });
    }

    if ($('#tblMr').length > 0) {
        var src = $('#tblMr').attr('data-get-ajax');
        $('#tblMr').dataTable({
            "destroy": true,
            "async": true,
            "processing": true,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "ajax": {
                "url": src,
                "dataSrc": function (json) {
                    return json;
                }
            }
        });
    }

    if ($('#tblBooking').length > 0) {
        var src = $('#tblBooking').attr('data-get-ajax');
        $('#tblBooking').dataTable({
            "destroy": true,
            "async": true,
            "processing": true,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "createdRow": function (row, data, dataIndex) {
                var status = data[5];

                if (status.includes("Confirmed")) {
                    $(row).addClass('bg-success-custom');
                } else if (status.includes("Pending")) {
                    $(row).addClass('bg-warning-custom');
                } else {
                    $(row).addClass('bg-danger-custom');
                }
            },
            "ajax": {
                "url": src,
                "dataSrc": function (json) {
                    return json;
                }
            }
        });
    }

    if ($('#tblDsr').length > 0) {
        var src = $('#tblDsr').attr('data-get-ajax');
        $('#tblDsr').dataTable({
            "destroy": true,
            "async": true,
            "processing": true,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "dom": 'Bfrtip',
            "buttons": [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            "ajax": {
                "url": src,
                "dataSrc": function (json) {
                    return json;
                }
            }
        });
    }

    if ($('#tblVclBooking').length > 0) {
        var src = $('#tblVclBooking').attr('data-get-ajax');
        $('#tblVclBooking').dataTable({
            "destroy": true,
            "async": true,
            "processing": true,
            "autoWidth": false,
            "responsive": true,
            "paging": true,
            "searching": true,
            "createdRow": function (row, data, dataIndex) {
                var status = data[4];

                if (status.includes("Confirmed")) {
                    $(row).addClass('bg-success-custom');
                } else if (status.includes("Pending")) {
                    $(row).addClass('bg-warning-custom');
                } else {
                    $(row).addClass('bg-danger-custom');
                }
            },
            "ajax": {
                "url": src,
                "dataSrc": function (json) {
                    return json;
                }
            }
        });
    }

    $('.btn3').click(function (event) {
        event.preventDefault();

        var form = $(this).closest('form')[0];

        if (form.checkValidity()) {
            $(this).html('Saving...');
            $(this).prop('disabled', true);
            form.submit();
        } else {
            $(this).closest('form').addClass('was-validated');
        }
    });
});

function verDet() {
    var verId = $("#ver").val();
    if ((verId !== '1') && (verId !== '0')) {
        $.ajax({
            url: AbsUrl + 'user/ver-det-updt/' + verId + '/',
            type: "POST",
            dataType: 'html',
            beforeSend: function () {
                $('#verDet').val('<div class="col-md-2">Loading Vertical details...</div>');
            },
            success: function (data) {
                $('#verDet').html(data);
            }
        });
    } else {
        $('#verDet').html('');
    }
}

function verDetEdt() {
    var verId = $("#verEdit").val();
    $('#verDetEdt').html('');
    if ((verId !== '1') && (verId !== '0')) {
        $.ajax({
            url: AbsUrl + 'user/ver-det-updt-edit/' + verId + '/',
            type: "POST",
            dataType: 'html',
            beforeSend: function () {
                $('#verDetEdt').val('<div class="col-md-2">Loading Vertical details...</div>');
            },
            success: function (data) {
                $('#verDetEdt').html(data);
            }
        });
    } else {
        $('#verDetEdt').html('');
    }
}

function vhName() {
    var usrType = $("#uType").val();
    if (usrType !== '1') {
        $("#vhName").removeClass("d-none");
    } else {
        $("#vhName").addClass("d-none");
    }
}

function vhNameEdt() {
    var usrType = $("#uTypeEdt").val();
    if (usrType !== '1') {
        $("#vhNameEdt").removeClass("d-none");
    } else {
        $("#vhNameEdt").addClass("d-none");
    }
}

function addItem() {
    var inc = parseInt($("#addItmBtn").attr("data-count"));
    var html = '<div id="addItem' + inc + '"><div class="form-group mb-3 row"><div class="col-md-6">'
            + '<label for="rmType' + inc + '" class="form-label"><strong>Room Type:</strong></label>'
            + '<input type="text" class="form-control" name="rmType' + inc + '" id="rmType' + inc + '" required="required" placeholder="Room Type">'
            + '<div class="invalid-feedback">Please Enter Room Type</div><div class="valid-feedback">Looks Good!</div></div>'
            + '<div class="col-md-6"><label for="rmNo' + inc + '" class="form-label"><strong>Number of Rooms:</strong></label>'
            + '<input type="number" class="form-control" name="rmNo' + inc + '" id="rmNo' + inc + '" placeholder="Numbers of Room">'
            + '<div class="invalid-feedback">Please Enter Number of Rooms</div><div class="valid-feedback">Looks Good!</div>'
            + '</div></div><hr></div>';
    ++inc;
    if (inc > 1) {
        $("#removeItmBtn").removeClass("d-none");
    }
    $('#addItmBtn').attr('data-count', inc);
    $("#addItm").append(html);
}
function removeItem() {
    var dec = parseInt($('#addItmBtn').attr('data-count'));
    --dec;
    $('#addItem' + dec).remove();
    if (dec < 2) {
        $("#removeItmBtn").addClass("d-none");
    }
    $('#addItmBtn').attr('data-count', dec);
}

function saveRoom(e) {
    e.preventDefault();
    $.ajax({
        url: $("#addRoom").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#addRoom")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#hotelTbl').DataTable().ajax.reload();
            $('#addRoom')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');

            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function saveUser(e) {
    e.preventDefault();
    $.ajax({
        url: $("#book1").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#book1")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#usrTbl').DataTable().ajax.reload();
            $('#verTbl').DataTable().ajax.reload();
            $('#book1')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');

            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function saveVertical(e) {
    e.preventDefault();
    $.ajax({
        url: $("#verForm").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#verForm")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#usrTbl').DataTable().ajax.reload();
            $('#verTbl').DataTable().ajax.reload();
            location.reload();
            $('#verForm')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');

            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function showRoomStatus(e) {
    e.preventDefault();
    $("#roomDet").html("");
    $.ajax({
        url: $("#roomSts").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#roomSts")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $("#roomDet").append(data);
            var roomDet = $('#top');
            var roomDetOffset = roomDet.offset().top;

            $(window).scroll(function () {
                if ($(window).scrollTop() >= roomDetOffset) {
                    roomDet.addClass('fixed-top');
                } else {
                    roomDet.removeClass('fixed-top');
                }
            });
        }
    });
}

function showVehicleStatus(e) {
    e.preventDefault();
    $("#roomDet").html("");
    $.ajax({
        url: $("#roomSts").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#roomSts")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $("#roomDet").append(data);
            var roomDet = $('#top');
            var roomDetOffset = roomDet.offset().top;

            $(window).scroll(function () {
                if ($(window).scrollTop() >= roomDetOffset) {
                    roomDet.addClass('fixed-top');
                } else {
                    roomDet.removeClass('fixed-top');
                }
            });
        }
    });
}

function updatePrice(e) {
    var tariff = parseInt((isNaN($('#tariff').val()) || ($('#tariff').val() === '')) ? 0 : $("#tariff").val());
    var disPrice = parseInt((isNaN(e.value) || (e.value === '')) ? 0 : e.value);
    var othrPrice = parseInt((isNaN($("#othrChrg").val()) || ($("#othrChrg").val() === '')) ? 0 : $("#othrChrg").val());
    var totPrice = tariff - disPrice;
    var grTotPrice = totPrice + othrPrice;
    var advance = parseInt((isNaN($("#advnc").val()) || ($("#advnc").val() === '')) ? 0 : $("#advnc").val());
    var balance = grTotPrice - advance;
    $("#total").val(totPrice);
    $("#grndTot").val(grTotPrice);
    $("#balance").val(balance);
}

function updateGrTot(e) {
    var tariff = parseInt((isNaN($('#tariff').val()) || ($('#tariff').val() === '')) ? 0 : $("#tariff").val());
    var disPrice = parseInt((isNaN($('#discount').val()) || ($('#discount').val() === '')) ? 0 : $("#discount").val());
    var othrPrice = parseInt((isNaN(e.value) || (e.value === '')) ? 0 : e.value);
    var totPrice = tariff - disPrice;
    var grTotPrice = totPrice + othrPrice;
    var advance = parseInt((isNaN($("#advnc").val()) || ($("#advnc").val() === '')) ? 0 : $("#advnc").val());
    var balance = grTotPrice - advance;
    $("#total").val(totPrice);
    $("#grndTot").val(grTotPrice);
    $("#balance").val(balance);
}

function updateGrPrice(e) {
    var tariff = parseInt((isNaN(e.value) || (e.value === '')) ? 0 : e.value);
    var disPrice = parseInt((isNaN($('#discount').val()) || ($('#discount').val() === '')) ? 0 : $("#discount").val());
    var othrPrice = parseInt((isNaN($("#othrChrg").val()) || ($("#othrChrg").val() === '')) ? 0 : $("#othrChrg").val());
    var totPrice = tariff - disPrice;
    var grTotPrice = totPrice + othrPrice;
    var advance = parseInt((isNaN($("#advnc").val()) || ($("#advnc").val() === '')) ? 0 : $("#advnc").val());
    var balance = grTotPrice - advance;
    $("#total").val(totPrice);
    $("#grndTot").val(grTotPrice);
    $("#balance").val(balance);
}

function updateBalance(e) {
    var tariff = parseInt((isNaN($('#tariff').val()) || ($('#tariff').val() === '')) ? 0 : $("#tariff").val());
    var disPrice = parseInt((isNaN($('#discount').val()) || ($('#discount').val() === '')) ? 0 : $("#discount").val());
    var othrPrice = parseInt((isNaN($("#othrChrg").val()) || ($("#othrChrg").val() === '')) ? 0 : $("#othrChrg").val());
    var totPrice = tariff - disPrice;
    var grTotPrice = totPrice + othrPrice;
    var advance = parseInt((isNaN(e.value) || (e.value === '')) ? 0 : e.value);
    var balance = grTotPrice - advance;
    $("#total").val(totPrice);
    $("#grndTot").val(grTotPrice);
    $("#balance").val(balance);
}

if ($("#book3").length > 0) {
    var tariff = parseInt((isNaN($('#tariff').val()) || ($('#tariff').val() === '')) ? 0 : $("#tariff").val());
    var disPrice = parseInt((isNaN($('#discount').val()) || ($('#discount').val() === '')) ? 0 : $("#discount").val());
    var othrPrice = parseInt((isNaN($("#othrChrg").val()) || ($("#othrChrg").val() === '')) ? 0 : $("#othrChrg").val());
    var totPrice = tariff - disPrice;
    var grTotPrice = totPrice + othrPrice;
    var advance = parseInt((isNaN($("#advnc").val()) || ($("#advnc").val() === '')) ? 0 : $("#advnc").val());
    var balance = grTotPrice - advance;
    $("#total").val(totPrice);
    $("#grndTot").val(grTotPrice);
    $("#balance").val(balance);
}

function updateAgentAdd(e) {
    $.ajax({
        url: AbsUrl + 'dsr/update-address/' + e.value + '/',
        type: "POST",
        dataType: 'html',
        beforeSend: function () {
            $('#desg').val('Loading Agent Designation...');
            $('#add').val('Loading Agent Address...');
            $('#phn').val('Loading Agent Contact Number...');
            $('#email').val('Loading Agent E-mail Id...');
        },
        success: function (data) {
            if (data !== null) {
                var splArr = data.split("|");
                $("#desg").val(splArr[0]);
                $('#add').val(splArr[1]);
                $('#phn').val(splArr[2]);
                $('#email').val(splArr[3]);
            } else {
                $('#desg').val('No data available');
                $('#add').val('No data available');
                $('#phn').val('No data available');
                $('#email').val('No data available');
            }
        }
    });
}

function updateAgentDet(e) {
    var urlMap = {
        '2': 'book/get-agent-det/2/',
        '3': 'book/get-agent-det/3/',
        '4': 'book/get-agent-det/4/',
        '5': 'book/get-agent-det/5/'
    };
    var url = urlMap[e.value] ? AbsUrl + urlMap[e.value] : '';
    if (url) {
        $("#agentDet").removeClass("d-none");
        $.ajax({
            url: url,
            type: "POST",
            dataType: 'html',
            beforeSend: function () {
                $('#agntName').html('<option value="">Loading Agent Names...</option>');
                $('#contPer').html('<option value="">Select Agent...</option>');
            },
            success: function (data) {
                if (data !== null) {
                    $("#agntName").html(data);
                } else {
                    $("#agntName").html('<option value="">No data available</option>');
                }
            }
        });
    } else {
        $("#agentDet").addClass("d-none");
    }
}

$('#agntName').on('change', function () {
    var selectedAgent = $(this).val();
    $.ajax({
        url: AbsUrl + 'book/get-contPer/' + selectedAgent + '/',
        type: "POST",
        dataType: 'html',
        beforeSend: function () {
            $('#contPer').html('<option value="">Loading Contact person Names...</option>');
        },
        success: function (data) {
            if (data !== null) {
                $("#contPer").html(data);
            } else {
                $("#contPer").html('<option value="">No data available</option>');
            }
        }
    });
});

function addContactFields() {
    var inc = parseInt($("#addContBtn").attr("data-count"));
    var html = '<div id="addAgent' + inc + '"><div class="form-group mb-3 row"><div class="col-md-3">'
            + '<label for="contName' + inc + '" class="form-label"><strong>Contact Person Name:</strong></label>'
            + '<input type="text" class="form-control" name="contName' + inc + '" id="contName' + inc + '" required="required" placeholder="Contact Person Name">'
            + '<div class="invalid-feedback">Please Enter Contact Person Name</div><div class="valid-feedback">Looks Good!</div></div>'
            + '<div class="col-md-3"><label for="desgn' + inc + '" class="form-label"><strong>Designation:</strong></label>'
            + '<input type="text" class="form-control" name="desgn' + inc + '" id="desgn' + inc + '" placeholder="Designation" required="required">'
            + '<div class="invalid-feedback">Please Enter Designation</div><div class="valid-feedback">Looks Good!</div></div>'
            + '<div class="col-md-3"><label for="email' + inc + '" class="form-label"><strong>Email:</strong></label>'
            + '<input type="email" class="form-control" name="email' + inc + '" id="email' + inc + '" placeholder="Email" required="required">'
            + '<div class="invalid-feedback">Please Enter Email</div><div class="valid-feedback">Looks Good!</div></div>'
            + '<div class="col-md-3"><label for="cont' + inc + '" class="form-label"><strong>Contact Number:</strong></label>'
            + '<input type="text" class="form-control" name="cont' + inc + '" id="cont' + inc + '" placeholder="Contact Number" required="required">'
            + '<div class="invalid-feedback">Please Enter Contact Number</div><div class="valid-feedback">Looks Good!</div></div></div><hr></div>';
    ++inc;
    if (inc > 1) {
        $("#removeItmBtn").removeClass("d-none");
    }
    $('#addContBtn').attr('data-count', inc);
    $("#addItm").append(html);
}

function removeContactFields() {
    var dec = parseInt($('#addContBtn').attr('data-count'));
    --dec;
    $('#addAgent' + dec).remove();
    if (dec < 2) {
        $("#removeItmBtn").addClass("d-none");
    }
    $('#addContBtn').attr('data-count', dec);
}

function saveAgent(event) {
    event.preventDefault();
    $.ajax({
        url: $("#addAgentForm").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#addAgentForm")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#agentTbl').DataTable().ajax.reload();
            $('#addAgentForm')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function addVehicle(event) {
    event.preventDefault();
    $.ajax({
        url: $("#saveVehicle").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#saveVehicle")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#vehicleTbl').DataTable().ajax.reload();
            $('#saveVehicle')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function addDriver(event) {
    event.preventDefault();
    $.ajax({
        url: $("#saveDriver").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#saveDriver")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#driverTbl').DataTable().ajax.reload();
            $('#saveDriver')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function saveBlkRm(event) {
    event.preventDefault();
    $.ajax({
        url: $("#blockRm").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#blockRm")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function saveBlkVcl(event) {
    event.preventDefault();
    $.ajax({
        url: $("#blockRm").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#blockRm")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            console.log(data);
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function cityDet() {
    $.ajax({
        url: AbsUrl + "agent/city-detls/",
        type: "POST",
        dataType: 'html',
        data: {
            pin: $("#pin").val()
        },
        success: function (data) {
            if (data !== null) {
                $("#city").val(data.trim());
            } else {
                $("#city").val("No data available");
            }
            $.ajax({
                url: AbsUrl + "agent/state-detls/",
                type: "POST",
                dataType: 'html',
                data: {
                    pin: $("#pin").val()
                },
                beforeSend: function () {
                    $('#stateData').html('<select class="form-select" name="state" id="state"><option value="">Loading States...</option></select>');
                },
                success: function (data1) {
                    $("#stateData").html("");
                    var stData = '<select class="form-select" name="state" id="state">'
                            + data1 + '</select>';
                    $("#stateData").html(stData);
                }
            });
        }
    });
}

function cityDetBook() {
    $.ajax({
        url: AbsUrl + "book/city-detls-book/",
        type: "POST",
        dataType: 'html',
        data: {
            pin: $("#pin").val()
        },
        success: function (data) {
            if (data !== null) {
                $("#city").val(data.trim());
            } else {
                $("#city").val("No data available");
            }
            $.ajax({
                url: AbsUrl + "book/state-detls-book/",
                type: "POST",
                dataType: 'html',
                data: {
                    pin: $("#pin").val()
                },
                beforeSend: function () {
                    $('#stateData').html('<select class="form-select" name="state" id="state"><option value="">Loading States...</option></select>');
                },
                success: function (data1) {
                    $("#stateData").html("");
                    var stData = '<select class="form-select" name="state" id="state">'
                            + data1 + '</select>';
                    $("#stateData").html(stData);
                }
            });
        }
    });
}

if ($("#pin").length > 0) {
    var searchInput = $("#pin");
    searchInput.autocomplete({
        source: function (request, response) {
            $.ajax({
                url: AbsUrl + "book/pin-search/",
                type: "POST",
                dataType: 'json',
                data: {
                    term: request.term,
                },
                success: function (data) {
                    response(data);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        },
        minLength: 2,
        delay: 100,
        select: function (event, ui) {
            searchInput.val(ui.item.value);
            return false;
        }
    });
}

$(document).on("click", ".room", function () {
    var room = $(this).data("room");
    var rmdet = $(this).data("rmdet");
    var hiddenInput = $('#roomid_' + rmdet);

    if ($(this).hasClass("bg-primary")) {
        $(this).removeClass("bg-primary").addClass("bg-success");
        alert("You have choosen Room " + room);
        hiddenInput.val(room);
    } else if ($(this).hasClass("bg-success")) {
        $(this).removeClass("bg-success").addClass("bg-primary");
        alert("You have unchoosen Room " + room);
        hiddenInput.val('0');
    } else if ($(this).hasClass("bg-info")) {
        alert("This Rooms is blocked");
    } else {
        alert("Room " + room + " is occupied.");
    }
});

$(document).on("click", ".vehicle", function () {
    var room = $(this).data("vclid");
    var rmdet = $(this).data("vcldet");
    var vclnum = $(this).data("vclnum");
    var hiddenInput = $('#vclid_' + rmdet);

    if ($(this).hasClass("bg-primary")) {
        $(this).removeClass("bg-primary").addClass("bg-success");
        alert("You have choosen Vehicle " + vclnum);
        hiddenInput.val(room);
        $('#driver_' + rmdet).prop('disabled', false);
    } else if ($(this).hasClass("bg-success")) {
        $(this).removeClass("bg-success").addClass("bg-primary");
        alert("You have unchoosen Vehicle " + vclnum);
        hiddenInput.val('0');
        $('#driver_' + rmdet).prop('disabled', true);
    } else if ($(this).hasClass("bg-info")) {
        alert("This Vehicle is blocked");
    } else {
        alert("Vehicle " + vclnum + " is occupied.");
    }
});

function showModal(bURL) {
    $('#rmStsModal').find('.modal-title').html('e - CRM');
    if (bURL === '') {
        $('#rmStsModal').find('.modal-body').html('<strong class="text-danger">Pleas supply a URL to fetch</strong>');
        $('#rmStsModal').modal('show');
        return false;
    }
    $.ajax({
        url: bURL,
        type: 'POST',
        timeout: 10000,
        dataType: "html",
        beforeSend: function () {
            $('#rmStsModal').find('.modal-body').html('<p><i class="fa-solid fa-spinner fa-spin-pulse"></i><span class="sr-only">Loading...</span> Getting data from server...<br/>Please Wait</p>');
            $('#rmStsModal').modal('show');
        },
        success: function (data) {
            $('#rmStsModal').find('.modal-body').html(data);
            $('#rmStsModal').modal('show');
            return false;
        }
    });
}

function saveFromModal(formId) {
    event.preventDefault();
    var $btn = $('#' + formId).find('button[type="button"]');
    var formData = $("#" + formId).serialize();
    $.ajax({
        url: $('#' + formId).attr('action'),
        type: 'POST',
        timeout: 10000,
        data: formData,
        dataType: "html",
        async: true,
        //processData: false,
        //contentType: false,
        beforeSend: function () {
            $btn.button('loading');
        },
        success: function (data) {
            $('#ErrMsg').html(data);
            $('#rmStsModal').modal('hide');
            return false;
        }
    });
}

function updateFromModal(formId) {
    event.preventDefault();
    var formData = $("#" + formId).serialize();
    $.ajax({
        url: $('#' + formId).attr('data-update'),
        type: 'POST',
        timeout: 10000,
        data: formData,
        dataType: "html",
        beforeSend: function () {

        },
        success: function (data) {
            $('#ErrMsg').html(data);
            $('#rmStsModal').modal('hide');
            return false;
        }
    });
}

function deleteFromModal(formId) {
    event.preventDefault();
    var formData = $("#" + formId).serialize();
    $.ajax({
        url: $('#' + formId).attr('data-delete'),
        type: 'POST',
        timeout: 10000,
        data: formData,
        dataType: "html",
        beforeSend: function () {

        },
        success: function (data) {
            $('#ErrMsg').html(data);
            $('#rmStsModal').modal('hide');
            return false;
        }
    });
}

function showModalMR(bURL) {
    $('#mrModal').find('.modal-title').html('Money Receipt');
    if (bURL === '') {
        $('#mrModal').find('.modal-body').html('<strong class="text-danger">Pleas supply a URL to fetch</strong>');
        $('#mrModal').modal('show');
        return false;
    }
    $.ajax({
        url: bURL,
        type: 'POST',
        timeout: 10000,
        dataType: "html",
        beforeSend: function () {
            $('#mrModal').find('.modal-body').html('<p><i class="fa-solid fa-spinner fa-spin-pulse"></i><span class="sr-only">Loading...</span> Getting data from server...<br/>Please Wait</p>');
            $('#mrModal').modal('show');
        },
        success: function (data) {
            $('#mrModal').find('.modal-body').html(data);
            $('#mrModal').modal('show');
            return false;
        }
    });
}

function showModalIndMR(bURL) {
    $('#mrModal').find('.modal-title').html('Money Receipt');
    if (bURL === '') {
        $('#mrModal').find('.modal-body').html('<strong class="text-danger">Pleas supply a URL to fetch</strong>');
        $('#mrModal').modal('show');
        return false;
    }
    $.ajax({
        url: bURL,
        type: 'POST',
        timeout: 10000,
        dataType: "html",
        beforeSend: function () {
            $('#mrModal').find('.modal-body').html('<p><i class="fa-solid fa-spinner fa-spin-pulse"></i><span class="sr-only">Loading...</span> Getting data from server...<br/>Please Wait</p>');
            $('#mrModal').modal('show');
        },
        success: function (data) {
            $('#mrModal').find('.modal-body').html(data);
            $('#mrModal').modal('show');
            return false;
        }
    });
}

function saveMr(event) {
    event.preventDefault();
    $.ajax({
        url: $("#makeMr").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#makeMr")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#makeMr')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function showModalUser(bURL) {
    $('#userModal').find('.modal-title').html('Update User');
    if (bURL === '') {
        $('#userModal').find('.modal-body').html('<strong class="text-danger">Pleas supply a URL to fetch</strong>');
        $('#userModal').modal('show');
        return false;
    }
    $.ajax({
        url: bURL,
        type: 'POST',
        timeout: 10000,
        dataType: "html",
        beforeSend: function () {
            $('#userModal').find('.modal-body').html('<p><i class="fa-solid fa-spinner fa-spin-pulse"></i><span class="sr-only">Loading...</span> Getting data from server...<br/>Please Wait</p>');
            $('#userModal').modal('show');
        },
        success: function (data) {
            $('#userModal').find('.modal-body').html(data);
            $('#userModal').modal('show');
            return false;
        }
    });
}

function showModalVertical(bURL) {
    $('#verticalEditModal').find('.modal-title').html('Update Vertical');
    if (bURL === '') {
        $('#verticalEditModal').find('.modal-body').html('<strong class="text-danger">Pleas supply a URL to fetch</strong>');
        $('#verticalEditModal').modal('show');
        return false;
    }
    $.ajax({
        url: bURL,
        type: 'POST',
        timeout: 10000,
        dataType: "html",
        beforeSend: function () {
            $('#verticalEditModal').find('.modal-body').html('<p><i class="fa-solid fa-spinner fa-spin-pulse"></i><span class="sr-only">Loading...</span> Getting data from server...<br/>Please Wait</p>');
            $('#verticalEditModal').modal('show');
        },
        success: function (data) {
            $('#verticalEditModal').find('.modal-body').html(data);
            $('#verticalEditModal').modal('show');
            return false;
        }
    });
}

function showModalAgent(bURL) {
    $('#agent1Modal').find('.modal-title').html('Update Agent');
    if (bURL === '') {
        $('#agent1Modal').find('.modal-body').html('<strong class="text-danger">Pleas supply a URL to fetch</strong>');
        $('#agent1Modal').modal('show');
        return false;
    }
    $.ajax({
        url: bURL,
        type: 'POST',
        timeout: 10000,
        dataType: "html",
        beforeSend: function () {
            $('#agent1Modal').find('.modal-body').html('<p><i class="fa-solid fa-spinner fa-spin-pulse"></i><span class="sr-only">Loading...</span> Getting data from server...<br/>Please Wait</p>');
            $('#agent1Modal').modal('show');
        },
        success: function (data) {
            $('#agent1Modal').find('.modal-body').html(data);
            $('#agent1Modal').modal('show');
            return false;
        }
    });
}

function updateUsr(event) {
    event.preventDefault();
    $.ajax({
        url: $("#updateUser").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#updateUser")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#usrTbl').DataTable().ajax.reload();
            $('#verTbl').DataTable().ajax.reload();
            $('#updateUser')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error("Status: " + status);
            console.error("Error: " + error);
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function updateVertical(event) {
    event.preventDefault();
    $.ajax({
        url: $("#verUpdtForm").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#verUpdtForm")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#usrTbl').DataTable().ajax.reload();
            $('#verTbl').DataTable().ajax.reload();
            $('#verUpdtForm')[0].reset();
            $('#book1')[0].reset();
            verDet();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error("Status: " + status);
            console.error("Error: " + error);
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function saveUpdatedAgent(event) {
    event.preventDefault();
    $.ajax({
        url: $("#addAgentForm1").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#addAgentForm1")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#agentTbl').DataTable().ajax.reload();
            $('#addAgentForm1')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error("Status: " + status);
            console.error("Error: " + error);
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function showModalVehicle(bURL) {
    $('#vehicleModal').find('.modal-title').html('Update Vehicle');
    if (bURL === '') {
        $('#vehicleModal').find('.modal-body').html('<strong class="text-danger">Pleas supply a URL to fetch</strong>');
        $('#vehicleModal').modal('show');
        return false;
    }
    $.ajax({
        url: bURL,
        type: 'POST',
        timeout: 10000,
        dataType: "html",
        beforeSend: function () {
            $('#vehicleModal').find('.modal-body').html('<p><i class="fa-solid fa-spinner fa-spin-pulse"></i><span class="sr-only">Loading...</span> Getting data from server...<br/>Please Wait</p>');
            $('#vehicleModal').modal('show');
        },
        success: function (data) {
            $('#vehicleModal').find('.modal-body').html(data);
            $('#vehicleModal').modal('show');
            return false;
        }
    });
}

function showModalDriver(bURL) {
    $('#driverModal').find('.modal-title').html('Update Driver');
    if (bURL === '') {
        $('#driverModal').find('.modal-body').html('<strong class="text-danger">Pleas supply a URL to fetch</strong>');
        $('#driverModal').modal('show');
        return false;
    }
    $.ajax({
        url: bURL,
        type: 'POST',
        timeout: 10000,
        dataType: "html",
        beforeSend: function () {
            $('#driverModal').find('.modal-body').html('<p><i class="fa-solid fa-spinner fa-spin-pulse"></i><span class="sr-only">Loading...</span> Getting data from server...<br/>Please Wait</p>');
            $('#driverModal').modal('show');
        },
        success: function (data) {
            $('#driverModal').find('.modal-body').html(data);
            $('#driverModal').modal('show');
            return false;
        }
    });
}

function updateVhcl(event) {
    event.preventDefault();
    $.ajax({
        url: $("#updateVehicle").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#updateVehicle")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#vehicleTbl').DataTable().ajax.reload();
            $('#updateVehicle')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error("Status: " + status);
            console.error("Error: " + error);
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

function updateDrvr(event) {
    event.preventDefault();
    $.ajax({
        url: $("#updateDriver").attr("action"),
        type: 'POST',
        timeout: 10000,
        data: new FormData($("#updateDriver")[0]),
        dataType: "html",
        async: true,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#driverTbl').DataTable().ajax.reload();
            $('#updateDriver')[0].reset();
            if (data.status !== '') {
                $('#ErrMsg').html(data).fadeIn('slow');
            } else {
                $('#ErrMsg').html(data).fadeIn('slow');
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            console.error("Status: " + status);
            console.error("Error: " + error);
        }
    }).done(function (data) {
        $('.modal.fade').find('.btn-close').click();
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // Check if the URL contains a query parameter for a missing field
    const urlParams = new URLSearchParams(window.location.search);
    const missingField = urlParams.get('missing_field');

    // Focus on the missing field using its ID
    if (missingField) {
        document.getElementById(missingField).focus();
    }
});

if (UTYPE !== '1') {
    $("#setngMenu").addClass('d-none');
    $("#verticalModalBtn").addClass('d-none');
    $("#verDet").addClass('d-none');
    $("#hotels").addClass('d-none');
    $("#user").addClass('d-none');
    $("#veh").addClass('d-none');
    $("#driver").addClass('d-none');
}
