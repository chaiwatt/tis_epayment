  // สร้างฟังก์ชันสำหรับจัดกลุ่มข้อมูลตามประเภท
  function groupBy(array, key) {
    return array.reduce((result, currentValue) => {
        (result[currentValue[key]] = result[currentValue[key]] || []).push(currentValue);
        return result;
    }, {});
}

// ฟังก์ชันสำหรับการเรียงลำดับตาม calibration_branch.title
function sortTransactionsByTitle(transactionsGroup) {
    return transactionsGroup.sort((a, b) => {
        const titleA = a.calibration_branch?.title?.toUpperCase() || ''; // จัดการกรณีไม่มี title
        const titleB = b.calibration_branch?.title?.toUpperCase() || '';
        if (titleA < titleB) {
            return -1;
        }
        if (titleA > titleB) {
            return 1;
        }
        return 0;
    });
}



const facilityTypes = {
    'pl_2_1': 'สถานปฏิบัติการถาวร (Permanent facilities)',
    'pl_2_2': 'สถานปฏิบัติการนอกสถานที่ (Sites away from its permanent facilities)',
    'pl_2_3': 'สถานปฏิบัติการเคลื่อนที่ (Mobile facilities)',
    'pl_2_4': 'สถานปฏิบัติการชั่วคราว (Temporary facilities)',
    'pl_2_5': 'สถานปฏิบัติการหลายสถานที่ (Multi-site facilities)',
};

$(document).ready(function () {

    $('#parameter_one_textarea').summernote({
        height: 150, // กำหนดความสูงของ textarea
        toolbar: [
            ['para', ['ul', 'ol', 'paragraph']], // เพิ่มเฉพาะเครื่องมือที่ต้องการ
        ]
    });

    $('#parameter_two_textarea').summernote({
        height: 150, // กำหนดความสูงของ textarea
        toolbar: [
            ['para', ['ul', 'ol', 'paragraph']], // เพิ่มเฉพาะเครื่องมือที่ต้องการ
        ]
    });


    $('#cal_method_textarea').summernote({
        height: 200, // กำหนดความสูงของ textarea
        toolbar: [
            ['para', ['ul', 'ol', 'paragraph']], // เพิ่มเฉพาะเครื่องมือที่ต้องการ
        ]
    });

    createLabMainAddress();
    createLabAddressesArray();
    renderScopeTable();
});

$('#modal-add-parameter-one').on('shown.bs.modal', function () {
    // Destroy existing Summernote instance
    $('#parameter_one_textarea').summernote('destroy');
    
    // Reinitialize with desired settings
    $('#parameter_one_textarea').summernote({
        height: 150,
        toolbar: [
            ['para', ['ul', 'ol', 'paragraph']], // เพิ่มเครื่องมือจัดข้อความ
        ],
    });
});


$('#modal-add-parameter-two').on('shown.bs.modal', function () {
    // Destroy existing Summernote instance
    $('#parameter_two_textarea').summernote('destroy');
    
    // Reinitialize with desired settings
    $('#parameter_two_textarea').summernote({
        height: 150,
        toolbar: [
            ['para', ['ul', 'ol', 'paragraph']], // เพิ่มเครื่องมือจัดข้อความ
        ],
    });
});

$('#modal-add-cal-method').on('shown.bs.modal', function () {
    // Destroy existing Summernote instance
    $('#cal_method_textarea').summernote('destroy');
    
    // Reinitialize with desired settings
    $('#cal_method_textarea').summernote({
        height: 200,
        toolbar: [
            ['para', ['ul', 'ol', 'paragraph']], // เพิ่มเครื่องมือจัดข้อความ
        ],
    });
});



    // ฟังก์ชันสำหรับการดึงคำอธิบายประเภทสถานที่
    function getFacilityTypeDescription(input) {
        // ตัดคำว่า '_branch' หรือ '_main' ออกจาก input
        const key = input.replace(/_(branch|main)$/, '');
        // คืนค่าคำอธิบายประเภทสถานที่ ถ้าไม่เจอให้คืนค่า 'Unknown facility type'
        return facilityTypes[key] || 'Unknown facility type';
    }

    // ฟังก์ชันสำหรับการแสดงข้อมูลของสำนักงานใหญ่
    function renderMainTransactions(mainTransactions) {
        let groupedTransactions = groupBy(mainTransactions, 'site_type');

        var back = false;

        // ตรวจสอบว่ามีข้อมูลอยู่หรือไม่
        if (certificateHistorys && certificateHistorys.length > 0) {
            // ดึงรายการสุดท้ายจาก certificateHistorys
            var lastItem = certificateHistorys[certificateHistorys.length - 1];
            
            // ตรวจสอบเงื่อนไขของรายการสุดท้าย
            if (lastItem.check_status === 1 && lastItem.status_scope === 1) {
                // ถ้าเงื่อนไขเป็นจริง
                console.log('Last item meets the condition: check_status == 1 && status_scope == 1');
                back = true;
            }
    
            // แสดงค่าของ back
            console.log('Value of back:', back);
        }

        let html = '<h4 style="margin-bottom: 20px !important">ขอบข่ายที่ยื่นขอรับการรับรองสำหรับสำนักงานใหญ่</h4>';
        
        $.each(groupedTransactions, function (siteType, transactionsGroup) {
            transactionsGroup = sortTransactionsByTitle(transactionsGroup); // เรียงลำดับข้อมูลตาม title
            html += '<h4 class="text-success" style="margin-left: 20px !important"> - ' + getFacilityTypeDescription(siteType) + '</h4>';
            html += `<a class="btn btn-info pull-right update_scope" data-branch="${undefined}" data-id="${siteType}" style="margin-bottom: 10px;"><i class="icon-plus"></i> ปรับปรุง</a>`;
            html += '<table class="table table-bordered"><thead class="bg-primary"><tr>';
            html += '<th class="text-center text-white" width="15%">สาขาทดสอบ</th>';
            html += '<th class="text-center text-white" width="20%">หมวดหมู่เครื่องมือ</th>';
            html += '<th class="text-center text-white" width="15%">เครื่องมือ</th>';
            html += '<th class="text-center text-white" width="15%">พารามิเตอร์1</th>';
            html += '<th class="text-center text-white" width="15%">พารามิเตอร์2</th>';
            html += '<th class="text-center text-white" width="20%">วิธีสอบเทียบ</th>';
            html += '</tr></thead><tbody>';
        
            $.each(transactionsGroup, function (index, transaction) {
                html += '<tr>';
                html += '<td>' + (transaction.calibration_branch?.title ?? '') + ' (' + (transaction.calibration_branch?.title_en ?? '') + ')</td>';
                html += '<td>' + (transaction.calibration_branch_instrument_group?.name ?? '') + '</td>';
                html += '<td>' + (transaction.calibration_branch_instrument?.name ?? '') + '</td>';
                html += '<td>' + (transaction.calibration_branch_param1?.name ?? '') + (transaction.parameter_one_value ?? '') + '</td>';
                html += '<td>' + (transaction.calibration_branch_param2?.name ?? '') + (transaction.parameter_two_value ?? '') + '</td>';
                html += '<td>' + (transaction.cal_method ?? '') + '</td>';
                html += '</tr>';
            });
        
            html += '</tbody></table>';
        });
        

        $('#scope_wrapper').append(html);
    }

    function renderLabTypesBranchTransactions(branchLabAdresses, labAddressesArray,wrapper) {
        let html = '<h4 style="margin-bottom: 20px; margin-top:50px; !important">ขอบข่ายที่ยื่นขอรับการรับรองสำหรับสาขา</h4>';
    
        var back = false;

        // ตรวจสอบว่ามีข้อมูลอยู่หรือไม่
        if (certificateHistorys && certificateHistorys.length > 0) {
            // ดึงรายการสุดท้ายจาก certificateHistorys
            var lastItem = certificateHistorys[certificateHistorys.length - 1];
            
            // ตรวจสอบเงื่อนไขของรายการสุดท้าย
            if (lastItem.check_status === 1 && lastItem.status_scope === 1) {
                // ถ้าเงื่อนไขเป็นจริง
                console.log('Last item meets the condition: check_status == 1 && status_scope == 1');
                back = true;
            }

            // แสดงค่าของ back
            console.log('Value of back:', back);
        }

        // Loop through each branchLabAdresses and match with labAddressesArray
        $.each(branchLabAdresses, function (branchIndex, branchLabAdresse) {
            let labAddress = labAddressesArray[branchIndex]; // Match with labAddressesArray using the same index
    
            if (labAddress && labAddress.lab_types) {
                html += '<h4 class="text-warning" style="margin-top: 20px !important">สาขา: ';
                html += 'เลขที่ ' + (branchLabAdresse.addr_no ?? '') + ' หมู่ที่ ' + (branchLabAdresse.addr_moo ?? '') + ' ';
                html += 'แขวง/ตำบล' + (labAddress.sub_district_add_modal ?? '') + ' ';
                html += 'เขต/อำเภอ' + (labAddress.address_city_text_add_modal ?? '') + ' ';
                html += 'จังหวัด' + (labAddress.address_city_text_add_modal ?? '') + '</h4>';
    
                // Loop through each lab_type group in lab_types for the branch
                $.each(labAddress.lab_types, function (siteType, transactionsGroup) {
                    if (!Array.isArray(transactionsGroup) || transactionsGroup.length === 0) {
                        return; // Skip if the group is empty
                    }
    
                    // Sort transactionsGroup by cal_main_branch_text (alphabetically)
                    transactionsGroup.sort(function (a, b) {
                        return (a.cal_main_branch_text || '').localeCompare(b.cal_main_branch_text || '');
                    });
    
                    html += '<h4 class="text-success" style="margin-left: 20px !important"> - ' + getFacilityTypeDescription(siteType) + '</h4>';

                    if (!back || labCalScopeTransactionGroups === null) {
                        html += `<a class="btn btn-info pull-right update_scope" data-branch="${branchLabAdresse.id}" data-branch_index="${branchIndex}" data-id="${siteType}" style="margin-bottom: 10px;"><i class="icon-plus"></i> ปรับปรุง</a>`;
                    }

                    html += '<table class="table table-bordered"><thead class="bg-primary"><tr>';
                    html += '<th class="text-center text-white" width="15%">สาขาทดสอบ</th>';
                    html += '<th class="text-center text-white" width="20%">หมวดหมู่เครื่องมือ</th>';
                    html += '<th class="text-center text-white" width="15%">เครื่องมือ</th>';
                    html += '<th class="text-center text-white" width="15%">พารามิเตอร์1</th>';
                    html += '<th class="text-center text-white" width="15%">พารามิเตอร์2</th>';
                    html += '<th class="text-center text-white" width="20%">วิธีสอบเทียบ</th>';
                    html += '</tr></thead><tbody>';
    
                    $.each(transactionsGroup, function (index, transaction) {
                        html += '<tr>';
                        html += '<td>' + (transaction.cal_main_branch_text ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_instrumentgroup_text ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_instrument_text ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_parameter_one_text ?? '') + (transaction.cal_parameter_one_value ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_parameter_two_text ?? '') + (transaction.cal_parameter_two_value ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_method ?? '') + '</td>';
                        html += '</tr>';
                    });
    
                    html += '</tbody></table>';
                });
            }
        });

        if (labCalScopeTransactionGroups && labCalScopeTransactionGroups.length !== 0) {
            html += '<div class="clearfix"></div>';
            html += '<div class="row">';
            html += '  <legend>';
            html += '    <h4>รายการขอบข่ายที่แก้ไข</h4>';
            html += '  </legend>';
            html += '  <div class="col-md-12 col-md-offset-1">'; // เพิ่ม offset 1 ที่นี่
            html += '    <div class="form-group">';
        
            let firstLoop = true;
            // วนลูปข้อมูลจาก labCalScopeTransactionGroups
            labCalScopeTransactionGroups.forEach(function (labCalScopeTransactionGroup) {
                html += '<a type="button" class="btn btn-warning btn-scope-group" style="margin-right:10px" ';
                html += 'data-certi_lab="' + certi_lab.id + '" ';
                html += 'data-group="' + labCalScopeTransactionGroup.group + '" ';
                html += 'data-created_at="' + labCalScopeTransactionGroup.created_at + '">';
                
                // ถ้าเป็นรอบแรก ให้เพิ่ม "(ตั้งต้น)"
                if (firstLoop) {
                    html += 'ครั้งที่ ' + labCalScopeTransactionGroup.group + ' (ตั้งต้น)';
                    firstLoop = false; // เปลี่ยนสถานะหลังจากผ่านรอบแรกแล้ว
                } else {
                    html += 'ครั้งที่ ' + labCalScopeTransactionGroup.group;
                }
            
                html += '</a>';
            });
        
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
        
        }
        $(wrapper).append(html);
        // $(wrapper).append(html);
    }
    

    function renderLabTypesBranchTransactionsModal(branchLabAdresses, labAddressesArray,wrapper) {
        let html = '<h4 style="margin-bottom: 20px; margin-top:50px; !important">ขอบข่ายที่ยื่นขอรับการรับรองสำหรับสาขา</h4>';
    
        var back = false;

        // ตรวจสอบว่ามีข้อมูลอยู่หรือไม่
        if (certificateHistorys && certificateHistorys.length > 0) {
            // ดึงรายการสุดท้ายจาก certificateHistorys
            var lastItem = certificateHistorys[certificateHistorys.length - 1];
            
            // ตรวจสอบเงื่อนไขของรายการสุดท้าย
            if (lastItem.check_status === 1 && lastItem.status_scope === 1) {
                // ถ้าเงื่อนไขเป็นจริง
                console.log('Last item meets the condition: check_status == 1 && status_scope == 1');
                back = true;
            }

            // แสดงค่าของ back
            console.log('Value of back:', back);
        }

        // Loop through each branchLabAdresses and match with labAddressesArray
        $.each(branchLabAdresses, function (branchIndex, branchLabAdresse) {
            let labAddress = labAddressesArray[branchIndex]; // Match with labAddressesArray using the same index
    
            if (labAddress && labAddress.lab_types) {
                html += '<h4 class="text-warning" style="margin-top: 20px !important">สาขา: ';
                html += 'เลขที่ ' + (branchLabAdresse.addr_no ?? '') + ' หมู่ที่ ' + (branchLabAdresse.addr_moo ?? '') + ' ';
                html += 'แขวง/ตำบล' + (labAddress.sub_district_add_modal ?? '') + ' ';
                html += 'เขต/อำเภอ' + (labAddress.address_city_text_add_modal ?? '') + ' ';
                html += 'จังหวัด' + (labAddress.address_city_text_add_modal ?? '') + '</h4>';
    
                // Loop through each lab_type group in lab_types for the branch
                $.each(labAddress.lab_types, function (siteType, transactionsGroup) {
                    if (!Array.isArray(transactionsGroup) || transactionsGroup.length === 0) {
                        return; // Skip if the group is empty
                    }
    
                    // Sort transactionsGroup by cal_main_branch_text (alphabetically)
                    transactionsGroup.sort(function (a, b) {
                        return (a.cal_main_branch_text || '').localeCompare(b.cal_main_branch_text || '');
                    });
    
                    html += '<h4 class="text-success" style="margin-left: 20px !important"> - ' + getFacilityTypeDescription(siteType) + '</h4>';


                    html += '<table class="table table-bordered"><thead class="bg-primary"><tr>';
                    html += '<th class="text-center text-white" width="15%">สาขาทดสอบ</th>';
                    html += '<th class="text-center text-white" width="20%">หมวดหมู่เครื่องมือ</th>';
                    html += '<th class="text-center text-white" width="15%">เครื่องมือ</th>';
                    html += '<th class="text-center text-white" width="15%">พารามิเตอร์1</th>';
                    html += '<th class="text-center text-white" width="15%">พารามิเตอร์2</th>';
                    html += '<th class="text-center text-white" width="20%">วิธีสอบเทียบ</th>';
                    html += '</tr></thead><tbody>';
    
                    $.each(transactionsGroup, function (index, transaction) {
                        html += '<tr>';
                        html += '<td>' + (transaction.cal_main_branch_text ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_instrumentgroup_text ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_instrument_text ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_parameter_one_text ?? '') + (transaction.cal_parameter_one_value ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_parameter_two_text ?? '') + (transaction.cal_parameter_two_value ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_method ?? '') + '</td>';
                        html += '</tr>';
                    });
    
                    html += '</tbody></table>';
                });
            }
        });

        $(wrapper).append(html);

    }

    function renderBranchTransactions(branchTransactions, branchLabAdresses) {
        let groupedByBranch = groupBy(branchTransactions, 'branch_lab_adress_id');
    
        let html = '<h4 style="margin-bottom: 20px; margin-top:50px; !important">ขอบข่ายที่ยื่นขอรับการรับรองสำหรับสาขา</h4>';
    
        $.each(groupedByBranch, function (branchAddressId, transactions) {
            let branchIndex = branchLabAdresses.findIndex(addr => addr.id === parseInt(branchAddressId)); // หาค่า index
            let branchLabAdresse = branchLabAdresses[branchIndex]; // ดึงค่า branchLabAdresse โดยตรง
    
            if (branchLabAdresse) {
                html += '<h4 class="text-warning" style="margin-top: 20px !important">สาขา: ';
                html += 'เลขที่ ' + (branchLabAdresse.addr_no ?? '') + ' หมู่ที่ ' + (branchLabAdresse.addr_moo ?? '') + ' ';
                html += 'แขวง/ตำบล' + (branchLabAdresse.district?.DISTRICT_NAME ?? '') + ' ';
                html += 'เขต/อำเภอ' + (branchLabAdresse.amphur?.AMPHUR_NAME ?? '') + ' ';
                html += 'จังหวัด' + (branchLabAdresse.province?.PROVINCE_NAME ?? '') + '</h4>';
    
                let groupedBySiteType = groupBy(transactions, 'site_type');
                $.each(groupedBySiteType, function (siteType, transactionsGroup) {
                    transactionsGroup = sortTransactionsByTitle(transactionsGroup); // เรียงลำดับข้อมูลตาม title
                    html += '<h4 class="text-success" style="margin-left: 20px !important"> - ' + getFacilityTypeDescription(siteType) + '</h4>';
                    html += `<a class="btn btn-info pull-right update_scope" data-branch="${branchAddressId}" data-branch_index="${branchIndex}" data-id="${siteType}" style="margin-bottom: 10px;"><i class="icon-plus"></i> ปรับปรุง</a>`;
                    html += '<table class="table table-bordered"><thead class="bg-primary"><tr>';
                    html += '<th class="text-center text-white" width="15%">สาขาทดสอบ</th>';
                    html += '<th class="text-center text-white" width="20%">หมวดหมู่เครื่องมือ</th>';
                    html += '<th class="text-center text-white" width="15%">เครื่องมือ</th>';
                    html += '<th class="text-center text-white" width="15%">พารามิเตอร์1</th>';
                    html += '<th class="text-center text-white" width="15%">พารามิเตอร์2</th>';
                    html += '<th class="text-center text-white" width="20%">วิธีสอบเทียบ</th>';
                    html += '</tr></thead><tbody>';
    
                    $.each(transactionsGroup, function (index, transaction) {
                        html += '<tr>';
                        html += '<td>' + (transaction.calibration_branch?.title ?? '') + ' (' + (transaction.calibration_branch?.title_en ?? '') + ')</td>';
                        html += '<td>' + (transaction.calibration_branch_instrument_group?.name ?? '') + '</td>';
                        html += '<td>' + (transaction.calibration_branch_instrument?.name ?? '') + '</td>';
                        html += '<td>' + (transaction.calibration_branch_param1?.name ?? '') + (transaction.parameter_one_value ?? '') + '</td>';
                        html += '<td>' + (transaction.calibration_branch_param2?.name ?? '') + (transaction.parameter_two_value ?? '') + '</td>';
                        html += '<td>' + (transaction.cal_method ?? '') + '</td>';
                        html += '</tr>';
                    });
    
                    html += '</tbody></table>';
                });
            }
        });
    
        $('#scope_wrapper').append(html);
    }
    

function renderScopeTable()
{
  $('#scope_wrapper').empty();
   lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
   lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];
   renderLabTypesMainTransactions(lab_main_address.lab_types,'#scope_wrapper');
   renderLabTypesBranchTransactions(branchLabAdresses, lab_addresses_array,'#scope_wrapper') 


}



function renderLabTypesMainTransactions(labTypes,wrapper) {
    let html = '<h4 style="margin-bottom: 20px !important">ขอบข่ายที่ยื่นขอรับการรับรอง</h4>';
    var back = false;
    // console.log('labTypes');
    // console.log(labTypes);
    // ตรวจสอบว่ามีข้อมูลอยู่หรือไม่
    if (certificateHistorys && certificateHistorys.length > 0) {
        // ดึงรายการสุดท้ายจาก certificateHistorys
        var lastItem = certificateHistorys[certificateHistorys.length - 1];
        
        // ตรวจสอบเงื่อนไขของรายการสุดท้าย
        if (lastItem.check_status === 1 && lastItem.status_scope === 1) {
            // ถ้าเงื่อนไขเป็นจริง
            console.log('Last item meets the condition: check_status == 1 && status_scope == 1');
            back = true;
        }

        // แสดงค่าของ back
        console.log('Value of back:', back);
    }

    // Loop through each lab_type group
    $.each(labTypes, function (siteType, transactionsGroup) {
        if (!Array.isArray(transactionsGroup) || transactionsGroup.length === 0) {
            return; // ข้ามถ้ากลุ่มว่างเปล่า
        }

        // เรียงลำดับ transactionsGroup โดยใช้ cal_main_branch_text (เรียงตามตัวอักษร)
        transactionsGroup.sort(function (a, b) {
            return (a.cal_main_branch_text || '').localeCompare(b.cal_main_branch_text || '');
        });

        html += '<h4 class="text-success" style="margin-left: 20px !important"> - ' + getFacilityTypeDescription(siteType) + '</h4>';

        if (!back) {
            html += `<a class="btn btn-info pull-right update_scope" data-branch="${undefined}" data-id="${siteType}" style="margin-bottom: 10px;"><i class="icon-plus"></i> ปรับปรุง</a>`;
        }

        // if (!back) {
            // html += `<a class="btn btn-info pull-right update_scope" data-branch="${undefined}" data-id="${siteType}" style="margin-bottom: 10px;"><i class="icon-plus"></i> ปรับปรุง</a>`;
        // }
        

        html += '<table class="table table-bordered"><thead class="bg-primary"><tr>';
        html += '<th class="text-center text-white" width="15%">สาขาทดสอบ</th>';
        html += '<th class="text-center text-white" width="20%">เครื่องมือ1</th>';
        html += '<th class="text-center text-white" width="15%">เครื่องมือ2</th>';
        html += '<th class="text-center text-white" width="15%">พารามิเตอร์1</th>';
        html += '<th class="text-center text-white" width="15%">พารามิเตอร์2</th>';
        html += '<th class="text-center text-white" width="20%">วิธีสอบเทียบ</th>';
        html += '</tr></thead><tbody>';

        $.each(transactionsGroup, function (index, transaction) {
            html += '<tr>';
            html += '<td>' + (transaction.cal_main_branch_text ?? '') + '</td>';
            html += '<td>' + (transaction.cal_instrumentgroup_text ?? '') + '</td>';
            html += '<td>' + (transaction.cal_instrument_text ?? '') + '</td>';
            html += '<td>' + (transaction.cal_parameter_one_text ?? '') + (transaction.cal_parameter_one_value ?? '') + '</td>';
            html += '<td>' + (transaction.cal_parameter_two_text ?? '') + (transaction.cal_parameter_two_value ?? '') + '</td>';
            html += '<td>' + (transaction.cal_method ?? '') + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
    });

    console.log('html');
    console.log(wrapper);
    // console.log(html);
    // Append HTML to the scope_wrapper
    $(wrapper).append(html);
    // return html
}


function renderLabTypesMainTransactionsModal(labTypes,wrapper) {
    let html = '<h4 style="margin-bottom: 20px !important">ขอบข่ายที่ยื่นขอรับการรับรอง</h4>';
    var back = false;
    // console.log('labTypes');
    // console.log(labTypes);
    // ตรวจสอบว่ามีข้อมูลอยู่หรือไม่
    if (certificateHistorys && certificateHistorys.length > 0) {
        // ดึงรายการสุดท้ายจาก certificateHistorys
        var lastItem = certificateHistorys[certificateHistorys.length - 1];
        
        // ตรวจสอบเงื่อนไขของรายการสุดท้าย
        if (lastItem.check_status === 1 && lastItem.status_scope === 1) {
            // ถ้าเงื่อนไขเป็นจริง
            console.log('Last item meets the condition: check_status == 1 && status_scope == 1');
            back = true;
        }

        // แสดงค่าของ back
        console.log('Value of back:', back);
    }

    // Loop through each lab_type group
    $.each(labTypes, function (siteType, transactionsGroup) {
        if (!Array.isArray(transactionsGroup) || transactionsGroup.length === 0) {
            return; // ข้ามถ้ากลุ่มว่างเปล่า
        }

        // เรียงลำดับ transactionsGroup โดยใช้ cal_main_branch_text (เรียงตามตัวอักษร)
        transactionsGroup.sort(function (a, b) {
            return (a.cal_main_branch_text || '').localeCompare(b.cal_main_branch_text || '');
        });

        html += '<h4 class="text-success" style="margin-left: 20px !important"> - ' + getFacilityTypeDescription(siteType) + '</h4>';


        html += '<table class="table table-bordered"><thead class="bg-primary"><tr>';
        html += '<th class="text-center text-white" width="15%">สาขาทดสอบ</th>';
        html += '<th class="text-center text-white" width="20%">หมวดหมู่เครื่องมือ</th>';
        html += '<th class="text-center text-white" width="15%">เครื่องมือ</th>';
        html += '<th class="text-center text-white" width="15%">พารามิเตอร์1</th>';
        html += '<th class="text-center text-white" width="15%">พารามิเตอร์2</th>';
        html += '<th class="text-center text-white" width="20%">วิธีสอบเทียบ</th>';
        html += '</tr></thead><tbody>';

        $.each(transactionsGroup, function (index, transaction) {
            html += '<tr>';
            html += '<td>' + (transaction.cal_main_branch_text ?? '') + '</td>';
            html += '<td>' + (transaction.cal_instrumentgroup_text ?? '') + '</td>';
            html += '<td>' + (transaction.cal_instrument_text ?? '') + '</td>';
            html += '<td>' + (transaction.cal_parameter_one_text ?? '') + (transaction.cal_parameter_one_value ?? '') + '</td>';
            html += '<td>' + (transaction.cal_parameter_two_text ?? '') + (transaction.cal_parameter_two_value ?? '') + '</td>';
            html += '<td>' + (transaction.cal_method ?? '') + '</td>';
            html += '</tr>';
        });

        html += '</tbody></table>';
    });

    console.log('html');
    console.log(wrapper);
    // console.log(html);
    // Append HTML to the scope_wrapper
    $(wrapper).append(html);
    // return html
}



$(document).on('click', '.update_scope', function(e) {
    const _token = $('input[name="_token"]').val();
  
    dataType = $(this).data('id');
    dataBranch = $(this).data('branch');
    branchIndex = $(this).data('branch_index');

    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];


    clearAndRewriteSelectWrapper();

    $('#cal_instrument_wrapper').hide();
    $('#cal_parameter_one_wrapper').hide();
    $('#cal_parameter_two_wrapper').hide();

    $.ajax({
        // url:"{{route('api.calibrate')}}",
        url:"/certify/api/calibrate",
        method:"POST",
        data:{
            _token:_token
        },
        success:function (result){
            // console.log(result);
            // ล้างค่าเดิมใน select element ก่อนเพิ่มค่าใหม่
            $('#cal_main_branch').empty();
            $('#cal_main_branch').append('<option value="not_select" disabled selected>- สาขาสอบเทียบ -</option>');

            $.each(result,function (index,value) {
                $('#cal_main_branch').append('<option value='+value.id+' >'+value.title+'</option>')
            });
            showAddCalScopeModal();
            
        }
    });
    

});


$(document).on('change', '#cal_main_branch', function() {
    var bcertify_calibration_branche_id = $(this).val();
    const _token = $('input[name="_token"]').val();
    $('#cal_instrument_wrapper').hide();
    $('#cal_parameter_one_wrapper').hide();
    $('#cal_parameter_two_wrapper').hide();

    $.ajax({
        // url: "{{route('api.instrumentgroup')}}",
        url: "/certify/api/instrumentgroup",
        method: "POST",
        data: {
            bcertify_calibration_branche_id: bcertify_calibration_branche_id,
            _token: _token
        },
        success: function(result) {
            // console.log(result);

            // Clear selected value and options
            $('#cal_instrumentgroup').val(null).trigger('change'); // Clear selected value
            $('#cal_instrumentgroup').select2('destroy').empty(); // Destroy select2 instance and clear options

            // Reinitialize select2 with an empty option
            $('#cal_instrumentgroup').append('<option value="not_select" disabled selected>- เลือกหมวดหมู่เครื่องมือ -</option>');
            
            $.each(result, function(index, value) {
                $('#cal_instrumentgroup').append('<option value=' + value.id + '>' + value.name + '</option>');
            });

            // Reinitialize select2
            $('#cal_instrumentgroup').select2();
        }
    });
});

$(document).on('change', '#cal_instrumentgroup', function() {
    var calibration_branch_instrument_group_id = $(this).val();
    const _token = $('input[name="_token"]').val();

    $.ajax({
        url: "/certify/api/instrument",
        method: "POST",
        data: {
            calibration_branch_instrument_group_id: calibration_branch_instrument_group_id,
            _token: _token
        },
        success: function (result) {
            // ตรวจสอบและแสดงหรือซ่อน wrapper ตามผลลัพธ์

            $('#cal_instrument').select2('destroy').empty();
            $('#cal_instrument').select2();

            $('#cal_parameter_one').select2('destroy').empty();
            $('#cal_parameter_one').select2();

            $('#cal_parameter_two').select2('destroy').empty();
            $('#cal_parameter_two').select2();

            if (result.instrument && result.instrument.length > 0) {
                $('#cal_instrument_wrapper').show();
                $('#cal_instrument').append('<option value="not_select" disabled selected>- เลือกเครื่องมือ -</option>');

                $.each(result.instrument, function (index, value) {
                    $('#cal_instrument').append('<option value=' + value.id + '>' + value.name + '</option>');
                });
            } else {

                $('#cal_instrument_wrapper').hide();
            }


            if (result.parameter_one && result.parameter_one.length > 0) {
                $('#cal_parameter_one_wrapper').show();
                $('#cal_parameter_one').append('<option value="not_select" disabled selected>- เลือกพารามิเตอร์1 -</option>');

                $.each(result.parameter_one, function (index, value) {
                    $('#cal_parameter_one').append('<option value=' + value.id + '>' + value.name + '</option>');
                });
            } else {
                $('#cal_parameter_one_wrapper').hide();
            }

            if (result.parameter_two && result.parameter_two.length > 0) {
                $('#cal_parameter_two_wrapper').show();
                $('#cal_parameter_two').append('<option value="not_select" disabled selected>- เลือกพารามิเตอร์2 -</option>');

                $.each(result.parameter_two, function (index, value) {
                    $('#cal_parameter_two').append('<option value=' + value.id + '>' + value.name + '</option>');
                });
            } else {
                $('#cal_parameter_two_wrapper').hide();
            }
        }
    });
});

$(document).on('click', '#button_add_cal_scope', function(e) {
    e.preventDefault();

    // เรียกใช้ฟังก์ชัน
    if (checkUnselectedOptions()) {
        alert('กรุณาเลือกรายการให้ครบถ้วน');
        return
    } 
    
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];
    
    if (dataBranch !== 'undefined')
    {
        // console.log('สาขา');
        // console.log(lab_addresses_array);
        var selectedValues = {
            cal_main_branch: $('#cal_main_branch').val() || '',
            cal_main_branch_text: $('#cal_main_branch').length ? $('#cal_main_branch option:selected').text() : '',
            
            cal_instrumentgroup: $('#cal_instrumentgroup').val() || '',
            cal_instrumentgroup_text: $('#cal_instrumentgroup').length ? $('#cal_instrumentgroup option:selected').text() : '',
            
            cal_instrument: $('#cal_instrument').val() || '',
            cal_instrument_text: $('#cal_instrument').length ? $('#cal_instrument option:selected').text() : '',
            
            cal_parameter_one: $('#cal_parameter_one').val() || '',
            cal_parameter_one_text: $('#cal_parameter_one').length ? $('#cal_parameter_one option:selected').text() : '',
            cal_parameter_one_value: '',
            
            cal_parameter_two: $('#cal_parameter_two').val() || '',
            cal_parameter_two_text: $('#cal_parameter_two').length ? $('#cal_parameter_two option:selected').text() : '',
            cal_parameter_two_value: '',

            cal_method: ''
        };

        

        if (lab_addresses_array[branchIndex]) {
            if (!Array.isArray(lab_addresses_array[branchIndex].lab_types[dataType])) {
                lab_addresses_array[branchIndex].lab_types[dataType] = [];
            }

            lab_addresses_array[branchIndex].lab_types[dataType].push(selectedValues);
        }

        sessionStorage.setItem('lab_addresses_array', JSON.stringify(lab_addresses_array));
        // console.log(lab_addresses_array);
        renderCalScopeTable(dataBranch,dataType,branchIndex);
        renderScopeTable();
            
    }else {
        //สำนักงานใหญ่
        // console.log('สำนักงานใหญ่');
        // console.log(lab_main_address);

        var selectedValues = {
            cal_main_branch: $('#cal_main_branch').val() || '',
            cal_main_branch_text: $('#cal_main_branch').length ? $('#cal_main_branch option:selected').text() : '',
            
            cal_instrumentgroup: $('#cal_instrumentgroup').val() || '',
            cal_instrumentgroup_text: $('#cal_instrumentgroup').length ? $('#cal_instrumentgroup option:selected').text() : '',
            
            cal_instrument: $('#cal_instrument').val() || '',
            cal_instrument_text: $('#cal_instrument').length ? $('#cal_instrument option:selected').text() : '',
            
            cal_parameter_one: $('#cal_parameter_one').val() || '',
            cal_parameter_one_text: $('#cal_parameter_one').length ? $('#cal_parameter_one option:selected').text() : '',
            cal_parameter_one_value: '',
            
            cal_parameter_two: $('#cal_parameter_two').val() || '',
            cal_parameter_two_text: $('#cal_parameter_two').length ? $('#cal_parameter_two option:selected').text() : '',
            cal_parameter_two_value: '',

            cal_method: ''
        };

        // ดึงข้อมูล array จาก sessionStorage

        if (lab_main_address) {
            if (!Array.isArray(lab_main_address.lab_types[dataType])) {
                lab_main_address.lab_types[dataType] = [];
            }

            lab_main_address.lab_types[dataType].push(selectedValues);
        }

        sessionStorage.setItem('lab_main_address', JSON.stringify(lab_main_address));
        lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
        console.log(lab_main_address);
        renderCalScopeTable(dataBranch,dataType,branchIndex);
        renderScopeTable();
    }

});

$(document).on('click', '.btn-delete-scope-row', function(e) {
    e.preventDefault();
    // หาค่า index ของแถวที่ต้องการลบ
    var rowIndex = $(this).closest('tr').index();

    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];

    // console.log('dataType ', dataType);
    // console.log('dataBranch', dataBranch);
    // console.log('branchIndex', branchIndex);

    if (dataBranch !== 'undefined')
    {
        // console.log('สาขา');
        // console.log(lab_addresses_array);
        if (lab_addresses_array[branchIndex] && Array.isArray(lab_addresses_array[branchIndex].lab_types[dataType])) {
            var scopes = lab_addresses_array[branchIndex].lab_types[dataType];

            // ลบรายการที่เลือกจาก array
            scopes.splice(rowIndex, 1);
            
            // บันทึก array กลับไปที่ session storage
            sessionStorage.setItem('lab_addresses_array', JSON.stringify(lab_addresses_array));
            
            // console.log(lab_addresses_array);
            renderCalScopeTable(dataBranch,dataType,branchIndex);
            renderScopeTable();
        }
    }else{
        // console.log('สำนักงานใหญ่');
        // console.log(lab_main_address);
        if (lab_main_address && Array.isArray(lab_main_address.lab_types[dataType])) {
            var scopes = lab_main_address.lab_types[dataType];

            // ลบรายการที่เลือกจาก array
            scopes.splice(rowIndex, 1);
            
            // บันทึก array กลับไปที่ session storage
            sessionStorage.setItem('lab_main_address', JSON.stringify(lab_main_address));
            
            // เรียกใช้ฟังก์ชันเพื่ออัปเดตตาราง
            renderCalScopeTable(dataBranch,dataType,branchIndex);
            renderScopeTable();
        }
    }

});

$(document).on('click', '.btn-add-items-parameter-one', function(e) {
    e.preventDefault();
    // เก็บค่า data-index จากปุ่มที่ถูกกด
    selectedScopeIndex = $(this).data('index');
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];
    var parameterOneValue = '';
    if (dataBranch !== 'undefined')
    {
        // console.log('สาขา');
        var scopes = lab_addresses_array[branchIndex].lab_types[dataType];

        if (scopes && scopes[selectedScopeIndex]) {
            parameterOneValue = scopes[selectedScopeIndex].cal_parameter_one_value || '';
        }
    }else{
        // console.log('สำนักงานใหญ่');
        var scopes = lab_main_address.lab_types[dataType];
        if (scopes && scopes[selectedScopeIndex]) {
            parameterOneValue = scopes[selectedScopeIndex].cal_parameter_one_value || '';
        }
    }
        // อัปเดตค่าใน Summernote editor
        $('#parameter_one_textarea').summernote('code', parameterOneValue);

        // แสดง modal
        $('#modal-add-parameter-one').modal('show');
});

$(document).on('click', '#button_add_parameter_one', function(e) {
    e.preventDefault();
    var parameterOneText = $('#parameter_one_textarea').val().trim();
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];

    if (dataBranch !== 'undefined')
    {
        // console.log('สาขา');
        var scopes = lab_addresses_array[branchIndex].lab_types[dataType];
        var scope = scopes[selectedScopeIndex];
        if (scope && scope.cal_parameter_one_value !== undefined) {
            scope.cal_parameter_one_value = parameterOneText; // อัปเดตค่าของ cal_parameter_one_value
        }
        sessionStorage.setItem('lab_addresses_array', JSON.stringify(lab_addresses_array));
    }else{
        // console.log('สำนักงานใหญ่');
        var scopes = lab_main_address.lab_types[dataType];
        var scope = scopes[selectedScopeIndex];
        if (scope && scope.cal_parameter_one_value !== undefined) {
            scope.cal_parameter_one_value = parameterOneText; // อัปเดตค่าของ cal_parameter_one_value
        }
        sessionStorage.setItem('lab_main_address', JSON.stringify(lab_main_address));
    }

    renderCalScopeTable(dataBranch,dataType,branchIndex);
    renderScopeTable();

}); 

$(document).on('click', '.btn-add-items-parameter-two', function(e) {
    e.preventDefault();
    // เก็บค่า data-index จากปุ่มที่ถูกกด
    selectedScopeIndex = $(this).data('index');
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];
    var parameterTwoValue = '';
    if (dataBranch !== 'undefined')
    {
        // console.log('สาขา');
        var scopes = lab_addresses_array[branchIndex].lab_types[dataType];

        if (scopes && scopes[selectedScopeIndex]) {
            parameterTwoValue = scopes[selectedScopeIndex].cal_parameter_two_value || '';
        }
    }else{
        // console.log('สำนักงานใหญ่');
        var scopes = lab_main_address.lab_types[dataType];
        if (scopes && scopes[selectedScopeIndex]) {
            parameterTwoValue = scopes[selectedScopeIndex].cal_parameter_two_value || '';
        }
    }
        // อัปเดตค่าใน Summernote editor
        $('#parameter_two_textarea').summernote('code', parameterTwoValue);

        // แสดง modal
        $('#modal-add-parameter-two').modal('show');
});

$(document).on('click', '#button_add_parameter_two', function(e) {
    e.preventDefault();
    var parameterTwoText = $('#parameter_two_textarea').val().trim();
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];

    if (dataBranch !== 'undefined')
    {
        // console.log('สาขา');
        var scopes = lab_addresses_array[branchIndex].lab_types[dataType];
        var scope = scopes[selectedScopeIndex];
        if (scope && scope.cal_parameter_two_value !== undefined) {
            scope.cal_parameter_two_value = parameterTwoText; // อัปเดตค่าของ cal_parameter_two_value
        }
        sessionStorage.setItem('lab_addresses_array', JSON.stringify(lab_addresses_array));
    }else{
        // console.log('สำนักงานใหญ่');
        var scopes = lab_main_address.lab_types[dataType];
        var scope = scopes[selectedScopeIndex];
        if (scope && scope.cal_parameter_two_value !== undefined) {
            scope.cal_parameter_two_value = parameterTwoText; // อัปเดตค่าของ cal_parameter_two_value
        }
        sessionStorage.setItem('lab_main_address', JSON.stringify(lab_main_address));
    }

    renderCalScopeTable(dataBranch,dataType,branchIndex);
    renderScopeTable();

}); 

$(document).on('click', '.btn-add-items-cal-method', function(e) {
    e.preventDefault();
    selectedScopeIndex = $(this).data('index');
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];

    var calMethodValue = '';

    if (dataBranch !== 'undefined')
    {
        // console.log('สาขา');
        // console.log(lab_addresses_array);
        var scopes = lab_addresses_array[branchIndex].lab_types[dataType];
    
        // ตรวจสอบว่ามี scope ที่ selectedScopeIndex หรือไม่ และดึงค่า cal_method
        if (scopes && scopes[selectedScopeIndex]) {
            calMethodValue = scopes[selectedScopeIndex].cal_method || '';
        }
    }else{
        // console.log('สำนักงานใหญ่');
        var scopes = lab_main_address.lab_types[dataType];
    
        // ตรวจสอบว่ามี scope ที่ selectedScopeIndex หรือไม่ และดึงค่า cal_method
        if (scopes && scopes[selectedScopeIndex]) {
            calMethodValue = scopes[selectedScopeIndex].cal_method || '';
        }
    }
    // อัปเดตค่าใน Summernote editor
    $('#cal_method_textarea').summernote('code', calMethodValue);

    $('#modal-add-cal-method').modal('show');

});

$(document).on('click', '#button_add_cal_method', function(e) {
    e.preventDefault();
    var calMethodText = $('#cal_method_textarea').val().trim();
    // var lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address')) || { lab_types: {} };
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];

    if (dataBranch !== 'undefined')
    {
        var scopes = lab_addresses_array[branchIndex].lab_types[dataType];
        var scope = scopes[selectedScopeIndex];
        if (scope && scope.cal_method !== undefined) {
            scope.cal_method = calMethodText; // อัปเดตค่าของ cal_method
        }
        sessionStorage.setItem('lab_addresses_array', JSON.stringify(lab_addresses_array));
    }else{
        var scopes = lab_main_address.lab_types[dataType];
        var scope = scopes[selectedScopeIndex];
        if (scope && scope.cal_method !== undefined) {
            scope.cal_method = calMethodText; // อัปเดตค่าของ cal_method
        }
        sessionStorage.setItem('lab_main_address', JSON.stringify(lab_main_address));
    }

    renderCalScopeTable(dataBranch,dataType,branchIndex);
    renderScopeTable();
});

function checkUnselectedOptions() {
    var selectIds = ['#cal_main_branch', '#cal_instrumentgroup', '#cal_instrument', '#cal_parameter_one', '#cal_parameter_two'];
    
    for (var i = 0; i < selectIds.length; i++) {
        var selectId = selectIds[i];
        
        // ตรวจสอบว่า select มี option หรือไม่
        if ($(selectId + ' option').length > 0) {
            
            // ตรวจสอบว่ามี option ที่ selected หรือไม่ และถ้ามี value เท่ากับ "not_select"
            if ($(selectId + ' option:selected').val() === "not_select") {
                console.log(selectId + ' ยังไม่ได้เลือก option ที่ถูกต้อง');
                return true; // มี select ที่ไม่ได้เลือก option ที่ถูกต้อง
            }
        } else {
            console.log(selectId + ' ไม่มี option');
        }
    }
    
    return false; // ทุก select ถูกเลือก option ที่ถูกต้องแล้ว
}


function showAddCalScopeModal()
{
    
    renderCalScopeTable(dataBranch,dataType,branchIndex)
    $('#modal-add-cal-scope').modal('show');
}

function clearAndRewriteSelectWrapper() {
    // ลบเนื้อหาทั้งหมดใน select_wrapper
    $('#select_wrapper').html('');

    // เขียนโครงสร้างใหม่
    var newHtml = `
        <div class="col-md-4 form-group">
            <label for="">สาขาการทดสอบ</label>
            <select class="form-control" name="" id="cal_main_branch">
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="">หมวดหมู่เครื่องมือ</label>
            <select class="form-control" name="" id="cal_instrumentgroup">
            </select>
        </div>
        <div class="col-md-4 form-group" id="cal_instrument_wrapper">
            <label for="">เครื่องมือ</label>
            <select class="form-control" name="" id="cal_instrument">
            </select>
        </div>
        <div class="col-md-4 form-group" id="cal_parameter_one_wrapper">
            <label for="">พารามิเตอร์1</label>
            <select class="form-control" name="" id="cal_parameter_one">
            </select>
        </div>
        <div class="col-md-4 form-group" id="cal_parameter_two_wrapper">
            <label for="">พารามิเตอร์2</label>
            <select class="form-control" name="" id="cal_parameter_two">
            </select>
        </div>
    `;

    // ใส่โครงสร้างใหม่กลับเข้าไปใน select_wrapper
    $('#select_wrapper').html(newHtml);

    // Initialize Select2 สำหรับ select ที่ต้องการ
    $('#cal_main_branch, #cal_instrumentgroup, #cal_instrument, #cal_parameter_one, #cal_parameter_two').select2();
}

function createLabMainAddress()
{
    const labCalScopeMainTransactions = labCalScopeTransactions.filter(item => item.branch_lab_adress_id === null);
    const lab_main_address_server = {
        lab_type: 'main',
        branch_lab_adress_id: undefined,
        checkbox_main: '1',
        address_number_add: "",
        village_no_add: "",
        address_city_add: "",
        address_city_text_add: "",
        address_district_add: "",
        sub_district_add: "",
        postcode_add: "",
        lab_address_no_eng_add: "",
        lab_province_text_eng_add: "",
        lab_province_eng_add: "",
        lab_amphur_eng_add: "",
        lab_district_eng_add: "",
        lab_moo_eng_add: "",
        lab_soi_eng_add: "",
        lab_street_eng_add: "",
        lab_types: createLabTypesFromServer(labCalScopeMainTransactions,null,"main"), // เรียกใช้ฟังก์ชันเพื่อสร้าง lab_types
        address_soi_add: "",
        address_street_add: ""
    };

    lab_main_address = lab_main_address_server;

    sessionStorage.setItem('lab_main_address', JSON.stringify(lab_main_address_server));
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    // console.log(lab_main_address);
}
function createLabAddressesArray()
{
    const labCalScopeBranchTransactions  = labCalScopeTransactions.filter(item => item.branch_lab_adress_id !== null);
    const branchAddresses = [];
    
    branchLabAdresses.forEach(branchItem => {
        // console.log(branchItem);
        const lab_branch_address_server = {
            lab_type: 'branch',
            checkbox_main: '1',
            branch_lab_adress_id: branchItem.id,
            // thai
            address_number_add_modal: branchItem.addr_no || "",
            village_no_add_modal: branchItem.addr_moo || "",
            soi_add_modal: branchItem.addr_soi || "",
            road_add_modal: branchItem.addr_road || "",
            
            // จังหวัด
            address_city_add_modal: branchItem.province.PROVINCE_ID || "",
            address_city_text_add_modal: branchItem.province.PROVINCE_NAME || "",
            // อำเภอ
            address_district_add_modal: branchItem.amphur.AMPHUR_NAME || "",
            address_district_add_modal_id: branchItem.amphur.AMPHUR_ID || "",
            // ตำบล
            sub_district_add_modal: branchItem.district.DISTRICT_NAME || "",
            sub_district_add_modal_id: branchItem.district.DISTRICT_ID || "",
            // รหัสไปรษณีย์
            postcode_add_modal: branchItem.postal || "",

            // eng
            lab_address_no_eng_add_modal: branchItem.addr_no || "",
            lab_moo_eng_add_modal: branchItem.addr_moo_en || "",
            lab_soi_eng_add_modal: branchItem.addr_soi_en || "",
            lab_street_eng_add_modal: branchItem.addr_road_en || "",

            lab_province_eng_add_modal: branchItem.province.PROVINCE_ID || "",
            // อำเภอ
            lab_amphur_eng_add_modal: branchItem.amphur.AMPHUR_NAME_EN || "",
            // ตำบล
            lab_district_eng_add_modal: branchItem.district.DISTRICT_NAME_EN || "",
            
            lab_types: createLabTypesFromServer(labCalScopeBranchTransactions, branchItem.id, "branch"), // สำหรับสาขา
        };

        branchAddresses.push(lab_branch_address_server);
                
    });

    sessionStorage.setItem('lab_addresses_array', JSON.stringify(branchAddresses));

    // ดึงข้อมูลจาก session storage เมื่อเอกสารถูกโหลด
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];
    // console.log(lab_addresses_array);

}


function createLabTypesFromServer(serverData,branch_index,type) {
    var labTypes = {};

    if(type === 'main'){
        labTypes = {
            pl_2_1_main: 0, // index 0
            pl_2_2_main: 0, // index 1
            pl_2_3_main: 0, // index 2
            pl_2_4_main: 0, // index 3
            pl_2_5_main: 0  // index 4
        };
    }else if(type === 'branch'){

        labTypes = {
            pl_2_1_branch: 0, // index 0
            pl_2_2_branch: 0, // index 1
            pl_2_3_branch: 0, // index 2
            pl_2_4_branch: 0, // index 3
            pl_2_5_branch: 0  // index 4
        };
    }

    serverData.forEach(item => {
        const selectedValues = {
            cal_main_branch: item.calibration_branch ? item.calibration_branch.id : '',
            cal_main_branch_text: item.calibration_branch ? `${item.calibration_branch.title}` : '',

            cal_instrumentgroup: item.calibration_branch_instrument_group ? item.calibration_branch_instrument_group.id : '',
            cal_instrumentgroup_text: item.calibration_branch_instrument_group ? `${item.calibration_branch_instrument_group.name}` : '',

            cal_instrument: item.calibration_branch_instrument ? item.calibration_branch_instrument.id : '',
            cal_instrument_text: item.calibration_branch_instrument ? `${item.calibration_branch_instrument.name}` : '',

            cal_parameter_one: item.calibration_branch_param1 ? item.calibration_branch_param1.id : '',
            cal_parameter_one_text: item.calibration_branch_param1 ? `${item.calibration_branch_param1.name}` : '',
            cal_parameter_one_value: item.parameter_one_value || '',

            cal_parameter_two: item.calibration_branch_param2 ? item.calibration_branch_param2.id : '',
            cal_parameter_two_text: item.calibration_branch_param2 ? `${item.calibration_branch_param2.name}` : '',
            cal_parameter_two_value: item.parameter_two_value || '',

            cal_method: item.cal_method || ''
        };

        // ตรวจสอบว่า site_type มีใน labTypes หรือไม่
        if (item.site_type in labTypes) {
            // ถ้า labTypes ยังเป็น 0 (ยังไม่มีค่าใส่)
            if(type === 'main'){
                if (labTypes[item.site_type] === 0) {
                    labTypes[item.site_type] = [selectedValues]; // เริ่มต้นด้วย array ที่มี 1 ค่า
                } else {
                    labTypes[item.site_type].push(selectedValues); // เพิ่มค่าใน array
                }
            }else{


                if (parseInt(item.branch_lab_adress_id) === parseInt(branch_index)) {
                    if (labTypes[item.site_type] === 0) {
                        labTypes[item.site_type] = [selectedValues]; // เริ่มต้นด้วย array ที่มี 1 ค่า
                    } else {
                        labTypes[item.site_type].push(selectedValues); // เพิ่มค่าใน array
                    }
                }
            } 
        }
    });

    return labTypes;
}


function renderCalScopeTable(dataBranch,dataType,branchIndex) {
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];

    var scopes = null;

    if (dataBranch !== 'undefined')
    {
        //สาขา
        if (lab_addresses_array[branchIndex] && Array.isArray(lab_addresses_array[branchIndex].lab_types[dataType])) {
            console.log('ค้นหา scope สาขา แยกประเภท')
            // กรณี typeNumber ไม่ใช่ null และเป็น array ที่ต้องการ
            scopes = lab_addresses_array[branchIndex].lab_types[dataType];
        }    
    }else {
        //สำนักงานใหญ่
        if (lab_main_address && Array.isArray(lab_main_address.lab_types[dataType])) {
            // กรณี typeNumber ไม่ใช่ null และเป็น array ที่ต้องการ
            scopes = lab_main_address.lab_types[dataType];
        }  
    }

    $('#lab_cal_scope_body').empty();

    if (scopes) {
   
        var rows = [];

        scopes.forEach(function(scope, index) {
            var parameterOneButton = '';
            var parameterTwoButton = '';
            var parameterOneValue = '';
            var parameterTwoValue = '';
            var calMethodValue = '';
            
            // ตรวจสอบว่า scope.cal_parameter_one_text ไม่ใช่ค่าว่าง
            if (scope.cal_parameter_one_text !== '') {
                parameterOneButton = `<button type="button" class="btn btn-info btn-xs btn-add-items-parameter-one" data-index="${index}">
                                        <i class="fa fa-plus"></i>
                                    </button>`;
            }

            // ตรวจสอบว่า scope.cal_parameter_two_text ไม่ใช่ค่าว่าง
            if (scope.cal_parameter_two_text !== '') {
                parameterTwoButton = `<button type="button" class="btn btn-info btn-xs btn-add-items-parameter-two" data-index="${index}">
                                        <i class="fa fa-plus"></i>
                                    </button>`;
            }

            if (scope.cal_parameter_one_value !== '') {
                parameterOneValue = `${scope.cal_parameter_one_value}`;
            }

            if (scope.cal_parameter_two_value !== '') {
                parameterTwoValue = `${scope.cal_parameter_two_value}`;
            }

            if (scope.cal_method !== '') {
                calMethodValue = `${scope.cal_method}`;
            }

            var calMethodButton = `<button type="button" class="btn btn-info btn-xs btn-add-items-cal-method" data-index="${index}">
                                        <i class="fa fa-plus"></i>
                                    </button>`;
            var newRow = `<tr>
                <td>${scope.cal_main_branch_text}</td>
                <td>${scope.cal_instrumentgroup_text}</td>
                <td>${scope.cal_instrument_text}</td>
                <td>${scope.cal_parameter_one_text} ${parameterOneButton} ${parameterOneValue}</td>
                <td>${scope.cal_parameter_two_text} ${parameterTwoButton} ${parameterTwoValue}</td>
                <td>${calMethodButton} ${calMethodValue}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-xs btn-delete-scope-row" data-index="${index}">
                        <i class="fa fa-remove"></i>
                    </button>
                </td>
            </tr>`;

            // เก็บ newRow ไว้ใน array
            rows.push({
                branchText: scope.cal_main_branch_text,
                rowHtml: newRow
            });
        });

        // จัดเรียง array ตาม branchText
        rows.sort(function(a, b) {
            return a.branchText.localeCompare(b.branchText);
        });

        // render ตารางที่จัดเรียงแล้วไปยัง #lab_cal_scope_body
        rows.forEach(function(item) {
            $('#lab_cal_scope_body').append(item.rowHtml);
        });

    }
    // var lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address')) || { lab_types: {} };

    var tableContainer = $('#myTable_lab_cal_scope'); // ใช้ ID ของคอนเทนเนอร์ที่คุณใช้แสดงตาราง
    tableContainer.scrollTop(tableContainer[0].scrollHeight);
}



