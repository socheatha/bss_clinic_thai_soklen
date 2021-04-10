
function bss_number(number) {
    return (!number || typeof number == 'undefined' || number == 'undefined' || number == '0') ? 0 : parseInt(number);
}

function bss_string(txt) {
    return (!txt || typeof txt == 'undefined' || txt == 'undefined') ? '' : txt;
}

// calculate sum
function bss_sum_number() {
    let sum = 0;
    for (let i = 0; i < arguments.length; i++) {
        sum += bss_number(arguments[i]);
    }

    return bss_number(sum);
}

function bss_call_function(fc_name, clear_called = false) {
    if (typeof fc_name == 'function') {
        fc_name();
        if (clear_called) fc_name = function () { };
    }
}

function bss_swal_Success(title = '', text = '', fcCallBack) {
    Swal.fire({
        icon: 'success',
        title: bss_string(title),
        confirmButtonText: bss_string(text),
        timer: 1500
    }).then(() => {
        fcCallBack();
    });
}

function bss_swal_Error(txt) {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: bss_string(txt),
    });
}

function bss_swal_Warning(title = '', text = '', fcCallBack) {
    Swal.fire({
        icon: 'warning',
        title: bss_string(title),
        confirmButtonText: bss_string(text),
        // timer: 1500
    }).then(() => {
        fcCallBack();
    });
}

// prepare form AJAX submission
$(document).ready(function () {
    $(document).on('click', '.submitFormAjx', function (e) {
        e.preventDefault(); // prevent form native submission
        let _form = $(this).parents('form');

        $.ajax({
            url: _form.attr('action'),
            method: bss_string(_form.attr('method')),
            data: bss_string(_form.serialize()),
            success: function (res) {
                if (typeof onAjaxSuccess == 'string') {
                    bss_swal_Success(onAjaxSuccess);
                    onAjaxSuccess = '';
                } else if (typeof onAjaxSuccess == 'function') {
                    onAjaxSuccess(res); onAjaxSuccess = function () { };
                }
            },
            error: function (request, status, error) {
                bss_swal_Error(bss_string(request.responseText) + ' : ' + bss_string(status) + ' : ' + bss_string(error));
            }
        });
    });

    // date picker
    if ($('.bssDateRangePicker').length >= 1) {
        $('.bssDateRangePicker').daterangepicker(
            {
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month')
            },
            function (start, end) {
                $('#from').val(start.format('YYYY-MM-DD'));
                $('#to').val(end.format('YYYY-MM-DD'));
                getDatatable(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'))
            }
        )
    }

    $('#patient_id').change(function () {
		if ($(this).val()!='') {
			$.ajax({
				url: "/patient/getSelectDetail",
				type: 'post',
				data: { id : $(this).val() },
			})
			.done(function( result ) {
				$('[name="pt_name"]').val(result.patient.name);
				$('[name="pt_phone"]').val(result.patient.phone);
				$('[name="pt_age"]').val(result.patient.age);
				$('[name="pt_gender"]').val(result.patient.pt_gender);
				$('[name="pt_village"]').val(result.patient.address_village);
				$('[name="pt_commune"]').val(result.patient.address_commune);
				
                $('[name="pt_province_id"]').attr('data-district-id', result.patient.address_district_id);
				$('[name="pt_province_id"]').val(result.patient.address_province_id).trigger('change');
			});
		}
		
	});

    if ($('[name="pt_province_id"]').length >= 1) {
        $('[name="pt_province_id"]').change( function(e){
            _district_id = $(this).attr('data-district-id');
            $(this).attr('data-district-id', '');
            if ($(this).val() != '') {
                $.ajax({
                    url: "/province/getSelectDistrict",
                    method: 'post',
                    data: { id: $(this).val(), },
                    success: function (data) {
                        $('[name="pt_district_id"]').attr({"disabled":false});
                        $('[name="pt_district_id"]').html(data);
                        $('[name="pt_district_id"]').val(_district_id);
                    }
                });
            }else{
                $('[name="pt_district_id"]').attr({"disabled":true});
                $('[name="pt_district_id"]').html('<option value="">----------------------</option>');
                
            }
        });
    }


    $('#btn_upload').click(function () {
        $('#btn_upload').html('uploading data to BSS FTP server, please wait. <i class="fa fa-spinner fa-pulse"></i>');
        $.ajax({
            url: bss_string('/uplaoddb'),
            method: 'post',
            // async: false,
            complete: function(xhr, textStatus) {
                if (xhr.status == 200) {
                    $('#btn_upload').html('<p class="text-success">successfull uploaded. <i class="fa fa-check"></i></p>');
                } else {
                    $('#btn_upload').html('<p class="text-danger">problem while on uploading process. <i class="fa fa-times"></i></p>');
                }
            } 
        });
    });
});