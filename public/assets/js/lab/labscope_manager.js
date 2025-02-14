var baseUrl = $('meta[name="base-url"]').attr('content') + '/';
var globalTypeNumber = null;
var globalBranchNumber = null;
var globalBranchOffice = 'main';
var selectedScopeIndex;
var buttonColors = ['btn-primary', 'btn-success', 'btn-info', 'btn-warning', 'btn-danger'];
var lab_main_address;
var lab_branch_address;
var lab_addresses_array;

var attach_path;
let measurements = [];
let lab_cal_scope_data_transaction = []; // อาร์เรย์เก็บข้อมูลทั้งหมด

let test_measurements = [];
let lab_test_scope_data_transaction = []; // อาร์เรย์เก็บข้อมูลทั้งหมด

const facilityTypes = {
    'pl_2_1': 'สถานปฏิบัติการถาวร (Permanent facilities)',
    'pl_2_2': 'สถานปฏิบัติการนอกสถานที่ (Sites away from its permanent facilities)',
    'pl_2_3': 'สถานปฏิบัติการเคลื่อนที่ (Mobile facilities)',
    'pl_2_4': 'สถานปฏิบัติการชั่วคราว (Temporary facilities)',
    'pl_2_5': 'สถานปฏิบัติการหลายสถานที่ (Multi-site facilities)',
};

var branchFacilityTypes = [
    { text: "ประเภท1 สถานปฏิบัติการถาวร (Permanent facilities)", id: "pl_2_1_branch" },
    { text: "ประเภท2 สถานปฏิบัติการนอกสถานที่ (Sites away from its permanent facilities)", id: "pl_2_2_branch" },
    { text: "ประเภท3 สถานปฏิบัติการเคลื่อนที่ (Mobile facilities)", id: "pl_2_3_branch" },
    { text: "ประเภท4 สถานปฏิบัติการชั่วคราว (Temporary facilities)", id: "pl_2_4_branch" },
    { text: "ประเภท5 สถานปฏิบัติการหลายสถานที่ (Multi-site facilities)", id: "pl_2_5_branch" }
];

var mainFacilityTypes = [
    { text: "ประเภท1 สถานปฏิบัติการถาวร (Permanent facilities)", id: "pl_2_1_main" },
    { text: "ประเภท2 สถานปฏิบัติการนอกสถานที่ (Sites away from its permanent facilities)", id: "pl_2_2_main" },
    { text: "ประเภท3 สถานปฏิบัติการเคลื่อนที่ (Mobile facilities)", id: "pl_2_3_main" },
    { text: "ประเภท4 สถานปฏิบัติการชั่วคราว (Temporary facilities)", id: "pl_2_4_main" },
    { text: "ประเภท5 สถานปฏิบัติการหลายสถานที่ (Multi-site facilities)", id: "pl_2_5_main" }
];

function createDataFormat()
{
    console.log('this is labRequestMain',labRequestMain);
    console.log('this is labRequestBranchs',labRequestBranchs);
    // console.log('this is certi_lab type',labRequestMain.certi_lab.lab_type);

    if (labRequestMain) {
        if (labRequestMain.certi_lab.lab_type == 3){
            console.log("work on LAB test")

            const lab_main_address_server = {
                lab_type: 'main',
                checkbox_main: '1',
                address_number_add: labRequestMain.no || "",
                village_no_add: labRequestMain.moo || "",
                address_soi_add: labRequestMain.soi || "",
                address_street_add: labRequestMain.street || "",
                address_city_add: labRequestMain.province_id || "",
                address_city_text_add: labRequestMain.province_name || "",
                address_district_add: labRequestMain.amphur_name || "",
                sub_district_add: labRequestMain.tambol_name || "",
                postcode_add: labRequestMain.postal_code || "",
                lab_address_no_eng_add: labRequestMain.no_eng || "",
                lab_province_text_eng_add: labRequestMain.province_name_eng || "",
                lab_province_eng_add: labRequestMain.province_id || "",
                lab_amphur_eng_add: labRequestMain.amphur_name_eng || "",
                lab_district_eng_add: labRequestMain.tambol_name_eng || "",
                lab_moo_eng_add: labRequestMain.moo_eng || "",
                lab_soi_eng_add: labRequestMain.soi_eng || "",
                lab_street_eng_add: labRequestMain.street_eng || "",
                amphur_id_add: labRequestMain.amphur_id || "",
                tambol_id_add: labRequestMain.tambol_id || "",
                lab_types: createLabTestRequestFromServer(labRequestMain, "main"), // เรียกใช้ฟังก์ชันเพื่อสร้าง lab_types

            };
            
            sessionStorage.setItem('lab_main_address', JSON.stringify(lab_main_address_server));
            lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
      
            const lab_addresses_array_servers = [];
            labRequestBranchs.forEach(branch => {
               
                lab_addresses_array_servers.push({
                    lab_type: 'branch',
                    checkbox_branch: '1',
                    address_number_add_modal: branch.no || "",
                    village_no_add_modal: branch.moo || "",
                    soi_add_modal: branch.soi || "",
                    road_add_modal: branch.street || "",
                    // จังหวัด
                    address_city_add_modal: branch.province_id || "",
                    address_city_text_add_modal: branch.province_name || "",
                    // อำเภอ
                    address_district_add_modal: branch.amphur_name || "",
                    address_district_add_modal_id: branch.amphur_id || "",
                    // ตำบล
                    sub_district_add_modal: branch.tambol_name || "",
                    sub_district_add_modal_id: branch.tambol_id || "",
                    // รหัสไปรษณีย์
                    postcode_add_modal: branch.postal_code || "",
            
                    // eng
                    lab_address_no_eng_add_modal: branch.no_eng || "",
                    lab_moo_eng_add_modal: branch.moo_eng || "",
                    lab_soi_eng_add_modal: branch.soi_eng || "",
                    lab_street_eng_add_modal: branch.street_eng || "",
            
                    lab_province_eng_add_modal: branch.province_name_eng || "",
                    // อำเภอ
                    lab_amphur_eng_add_modal: branch.amphur_name_eng || "",
                    // ตำบล
                    lab_district_eng_add_modal: branch.tambol_name_eng || "",
            
                    lab_types: createLabTestRequestFromServer(branch, "branch"), // เรียกใช้ฟังก์ชันเพื่อสร้าง lab_types สำหรับสาขา
                });
            });

            sessionStorage.setItem('lab_addresses_array', JSON.stringify(lab_addresses_array_servers));
            lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];

            console.log('lab_addresses_array',lab_addresses_array);
            
        }else if(labRequestMain.certi_lab.lab_type == 4){
            console.log("work on LAB cal")
            // สำนักงานใหญ่
            const lab_main_address_server = {
                lab_type: 'main',
                checkbox_main: '1',
                address_number_add: labRequestMain.no || "",
                village_no_add: labRequestMain.moo || "",
                address_soi_add: labRequestMain.soi || "",
                address_street_add: labRequestMain.street || "",
                address_city_add: labRequestMain.province_id || "",
                address_city_text_add: labRequestMain.province_name || "",
                address_district_add: labRequestMain.amphur_name || "",
                sub_district_add: labRequestMain.tambol_name || "",
                postcode_add: labRequestMain.postal_code || "",
                lab_address_no_eng_add: labRequestMain.no_eng || "",
                lab_province_text_eng_add: labRequestMain.province_name_eng || "",
                lab_province_eng_add: labRequestMain.province_id || "",
                lab_amphur_eng_add: labRequestMain.amphur_name_eng || "",
                lab_district_eng_add: labRequestMain.tambol_name_eng || "",
                lab_moo_eng_add: labRequestMain.moo_eng || "",
                lab_soi_eng_add: labRequestMain.soi_eng || "",
                lab_street_eng_add: labRequestMain.street_eng || "",
                amphur_id_add: labRequestMain.amphur_id || "",
                tambol_id_add: labRequestMain.tambol_id || "",
                lab_types: createLabCalRequestFromServer(labRequestMain, "main"), // เรียกใช้ฟังก์ชันเพื่อสร้าง lab_types

            };

            sessionStorage.setItem('lab_main_address', JSON.stringify(lab_main_address_server));
            lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
            console.log('lab_main_address',lab_main_address.lab_types)

            const lab_addresses_array_servers = [];
            labRequestBranchs.forEach(branch => {
               
                lab_addresses_array_servers.push({
                    lab_type: 'branch',
                    checkbox_branch: '1',
                    address_number_add_modal: branch.no || "",
                    village_no_add_modal: branch.moo || "",
                    soi_add_modal: branch.soi || "",
                    road_add_modal: branch.street || "",
                    // จังหวัด
                    address_city_add_modal: branch.province_id || "",
                    address_city_text_add_modal: branch.province_name || "",
                    // อำเภอ
                    address_district_add_modal: branch.amphur_name || "",
                    address_district_add_modal_id: branch.amphur_id || "",
                    // ตำบล
                    sub_district_add_modal: branch.tambol_name || "",
                    sub_district_add_modal_id: branch.tambol_id || "",
                    // รหัสไปรษณีย์
                    postcode_add_modal: branch.postal_code || "",
            
                    // eng
                    lab_address_no_eng_add_modal: branch.no_eng || "",
                    lab_moo_eng_add_modal: branch.moo_eng || "",
                    lab_soi_eng_add_modal: branch.soi_eng || "",
                    lab_street_eng_add_modal: branch.street_eng || "",
            
                    lab_province_eng_add_modal: branch.province_name_eng || "",
                    // อำเภอ
                    lab_amphur_eng_add_modal: branch.amphur_name_eng || "",
                    // ตำบล
                    lab_district_eng_add_modal: branch.tambol_name_eng || "",
            
                    lab_types: createLabCalRequestFromServer(branch, "branch"), // เรียกใช้ฟังก์ชันเพื่อสร้าง lab_types สำหรับสาขา
                });
            });
            
            sessionStorage.setItem('lab_addresses_array', JSON.stringify(lab_addresses_array_servers));

            // ดึงข้อมูลจาก session storage เมื่อเอกสารถูกโหลด
            lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];

            console.log('lab_addresses_array',lab_addresses_array);

        }
    }
}

function createLabTestRequestFromServer(serverData, type) {
    // กำหนดโครงสร้างพื้นฐานของ labTypes
    let labTypes = {};
   
    // กำหนดค่าเริ่มต้นของ labTypes ตามประเภท (main หรือ branch) เป็น 0
    if (type === 'main') {
        labTypes = {
            pl_2_1_main: 0, // index 0
            pl_2_2_main: 0, // index 1
            pl_2_3_main: 0, // index 2
            pl_2_4_main: 0, // index 3
            pl_2_5_main: 0  // index 4
        };
    } else if (type === 'branch') {
        labTypes = {
            pl_2_1_branch: 0, // index 0
            pl_2_2_branch: 0, // index 1
            pl_2_3_branch: 0, // index 2
            pl_2_4_branch: 0, // index 3
            pl_2_5_branch: 0  // index 4
        };
    }
    
    // ตรวจสอบและเพิ่มข้อมูลจาก serverData.lab_test_transactions
    if (serverData && Array.isArray(serverData.lab_test_transactions)) {
        serverData.lab_test_transactions.forEach(transaction => {
            const key = transaction.key; // ใช้ key จาก transaction เพื่อระบุตำแหน่งใน labTypes

            // ตรวจสอบว่า key มีอยู่ใน labTypes หรือไม่
            if (labTypes[key] !== undefined) {
                // สร้าง transactionData
                const transactionData = {
                    index: transaction.index,
                    category: transaction.category,
                    category_th: transaction.category_th,
                    description: transaction.description,
                    standard: transaction.standard,
                    test_field: transaction.test_field,
                    test_field_eng: transaction.test_field_eng,
                    code: transaction.code,
                    measurements: []
                };

                // เพิ่ม measurements ลงใน transactionData
                if (transaction.lab_test_measurements) {
                    transaction.lab_test_measurements.forEach(measurement => {
                        const measurementData = {
                            name: measurement.name,
                            name_eng: measurement.name,
                            description: transaction.description,
                            detail: measurement.detail,
                            type: measurement.type,
                        };

                        transactionData.measurements.push(measurementData);
                    });
                }

                // ถ้า key ใน labTypes ยังเป็น 0 ให้เปลี่ยนเป็น array และเพิ่ม transactionData ลงไป
                if (labTypes[key] === 0) {
                    labTypes[key] = [transactionData];
                } else {
                    labTypes[key].push(transactionData);
                }
            }
        });
    }

    return labTypes;
}

function createLabCalRequestFromServer(serverData, type) {
    // กำหนดโครงสร้างพื้นฐานของ labTypes
    let labTypes = {};
   
    // กำหนดค่าเริ่มต้นของ labTypes ตามประเภท (main หรือ branch) เป็น 0
    if (type === 'main') {
        labTypes = {
            pl_2_1_main: 0, // index 0
            pl_2_2_main: 0, // index 1
            pl_2_3_main: 0, // index 2
            pl_2_4_main: 0, // index 3
            pl_2_5_main: 0  // index 4
        };
    } else if (type === 'branch') {
        labTypes = {
            pl_2_1_branch: 0, // index 0
            pl_2_2_branch: 0, // index 1
            pl_2_3_branch: 0, // index 2
            pl_2_4_branch: 0, // index 3
            pl_2_5_branch: 0  // index 4
        };
    }
    
    // ตรวจสอบและเพิ่มข้อมูลจาก serverData.lab_cal_transactions
    if (serverData && Array.isArray(serverData.lab_cal_transactions)) {
        serverData.lab_cal_transactions.forEach(transaction => {
            const key = transaction.key; // ใช้ key จาก transaction เพื่อระบุตำแหน่งใน labTypes

            // ตรวจสอบว่า key มีอยู่ใน labTypes หรือไม่
            if (labTypes[key] !== undefined) {
                // สร้าง transactionData
                const transactionData = {
                    index: transaction.index,
                    category: transaction.category,
                    category_th: transaction.category_th,
                    instrument: transaction.instrument,
                    instrument_text: transaction.instrument_text,
                    instrument_two: transaction.instrument_two,
                    instrument_two_text: transaction.instrument_two_text,
                    description: transaction.description,
                    standard: transaction.standard,
                    code: transaction.code,
                    type: transaction.type,
                    measurements: []
                };

                // เพิ่ม measurements ลงใน transactionData
                if (transaction.lab_cal_measurements) {
                    transaction.lab_cal_measurements.forEach(measurement => {
                        const measurementData = {
                            name: measurement.name,
                            ranges: []
                        };

                        // เพิ่ม ranges ลงใน measurementData
                        if (measurement.lab_cal_measurement_ranges) {
                            measurement.lab_cal_measurement_ranges.forEach(range => {
                                measurementData.ranges.push({
                                    description: range.description,
                                    range: range.range,
                                    uncertainty: range.uncertainty
                                });
                            });
                        }

                        transactionData.measurements.push(measurementData);
                    });
                }

                // ถ้า key ใน labTypes ยังเป็น 0 ให้เปลี่ยนเป็น array และเพิ่ม transactionData ลงไป
                if (labTypes[key] === 0) {
                    labTypes[key] = [transactionData];
                } else {
                    labTypes[key].push(transactionData);
                }
            }
        });
    }

    return labTypes;
}

function addCalScope(globalTypeNumber,globalBranchNumber,category,category_th,instrument,instrument_text,instrument_two,instrument_two_text,description,standard,code=2) {
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];
    // test_measurements = [];
    lab_cal_scope_data_transaction = [];
    // ดึงค่าจากฟอร์ม

    if (!category || category === "") {
        alert('กรุณาเลือกสาขาสอบเทียบ');
        return;  // หยุดการทำงานที่เหลือ
    }

    if (!instrument || instrument === "") {
        alert('กรุณาเลือกเครื่องมือ1');
        return;  // หยุดการทำงานที่เหลือ
    }

    let measurementsCopy = JSON.parse(JSON.stringify(measurements)); // คัดลอกค่า measurements ปัจจุบัน

    if (globalTypeNumber !== undefined && globalBranchNumber !== undefined) 
    {
        console.log('สาขาแยกขอบข่าย');

        let propertyName = `pl_2_${globalTypeNumber}_branch`;
        
        // ตรวจสอบว่า `propertyName` เป็น array หรือไม่
        if (!Array.isArray(lab_addresses_array[globalBranchNumber].lab_types[propertyName])) {
            lab_addresses_array[globalBranchNumber].lab_types[propertyName] = []; // ถ้าไม่ใช่ ให้ตั้งเป็น array ว่าง
        }

        // คำนวณ index จากลำดับของ array เดิม
        let index = lab_addresses_array[globalBranchNumber].lab_types[propertyName].length;

        // สร้างรายการใหม่
        let newEntry = {
            index: index,
            category: category,
            category_th: category_th,
            instrument: instrument,
            instrument_text: instrument_text,
            instrument_two: instrument_two,
            instrument_two_text: instrument_two_text,
            description: description,
            standard: standard,
            code: code,
            measurements: measurementsCopy,
        };

            // เพิ่มข้อมูลใหม่ใน lab_cal_scope_data_transaction
        lab_cal_scope_data_transaction.push(newEntry);
        lab_addresses_array[globalBranchNumber].lab_types[propertyName].push(newEntry)

        sessionStorage.setItem('lab_addresses_array', JSON.stringify(lab_addresses_array));
        lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];

    }
    else if(globalTypeNumber !== undefined && globalBranchNumber === undefined )
    {
        console.log('สำนักงานใหญ่ แยกขอบข่าย')
        // ค่า stationType ที่ต้องการ
        let propertyName = `pl_2_${globalTypeNumber}_main`;

        // ตรวจสอบว่า `propertyName` เป็น array หรือไม่
        if (!Array.isArray(lab_main_address.lab_types[propertyName])) {
            lab_main_address.lab_types[propertyName] = []; // ถ้าไม่ใช่ ให้ตั้งเป็น array ว่าง
        }

        // คำนวณ index จากลำดับของ array เดิม
        let index = lab_main_address.lab_types[propertyName].length;

        // สร้างรายการใหม่
        let newEntry = {
            index: index,
            category: category,
            category_th: category_th,
            instrument: instrument,
            instrument_text: instrument_text,
            instrument_two: instrument_two,
            instrument_two_text: instrument_two_text,
            description: description,
            standard: standard,
            code: code,
            measurements: measurementsCopy,
        };

        // เพิ่มข้อมูลใหม่ใน lab_cal_scope_data_transaction
        lab_cal_scope_data_transaction.push(newEntry);

        if (!Array.isArray(lab_main_address.lab_types[propertyName])) {
            lab_main_address.lab_types[propertyName] = [];
        }

        lab_main_address.lab_types[propertyName].push(newEntry);
       
        sessionStorage.setItem('lab_main_address', JSON.stringify(lab_main_address));
        lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address')) || [];

        console.log(`หลังเพิ่มข้อมูลใหม่:`, lab_main_address); 
    }

    // รีเซ็ต measurements
    measurements = [];
}

function createCalMeasurementData(description,rangeLines,uncertaintyLines)
{
    // หาจำนวนบรรทัดที่มากกว่า
    let maxLines = Math.max(rangeLines.length, uncertaintyLines.length);
    // เพิ่มข้อมูลลงใน ranges ของ measurement แรก
    for (let i = 0; i < maxLines; i++) {
        measurements[0].ranges.push({
            description: description,
            range: rangeLines[i] || '', // หากไม่มีบรรทัด ใช้ค่าว่าง
            uncertainty: uncertaintyLines[i] || '', // หากไม่มีบรรทัด ใช้ค่าว่าง
        });
    }
}

function removeMainRow(index, key) {
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
    lab_main_address.lab_types[key].splice(index, 1);

    sessionStorage.setItem('lab_main_address', JSON.stringify(lab_main_address));
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address'));
}

function removeBranchRow(index, key, branchIndex) {
    // ลบรายการออกจาก lab_test_scope_data_transaction ของสาขาที่กำหนด
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];
    lab_addresses_array[branchIndex].lab_types[key].splice(index, 1);

    sessionStorage.setItem('lab_addresses_array', JSON.stringify(lab_addresses_array));
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];
}

function renderCalScopeWithParameterTable(_wrapper,hidden_delete_button=true) 
{
    // alert('ooo');
    let wrapper = $(_wrapper);
    wrapper.empty(); // ล้างข้อมูลเก่าก่อน
    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address')) || [];
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];
    let labTypes = lab_main_address.lab_types; // ดึง lab_types ออกมา

    // console.log('lab_addresses_arraydddd',lab_addresses_array)
    // alert('pk');

    for (let i = 1; i <= 5; i++) { // วนลูปตั้งแต่ 1 ถึง 5
        let key = `pl_2_${i}_main`; // สร้างชื่อ key เช่น pl_2_1_main, pl_2_2_main

        if (Array.isArray(labTypes[key])) { // ตรวจสอบว่าค่าเป็น array หรือไม่

            let facilityType = mainFacilityTypes.find(type => type.id === key); // ค้นหา label จาก mainFacilityTypes
            let labelText = facilityType ? facilityType.text : "ไม่ทราบประเภท"; // ตรวจสอบว่าเจอหรือไม่

            let lab_cal_scope_data_transaction = labTypes[key];
            // สร้างตัวแปรสำหรับนับจำนวน category


                    // จัดเรียงข้อมูลตาม category
            lab_cal_scope_data_transaction.sort((a, b) => {
                if (a.category < b.category) return -1;
                if (a.category > b.category) return 1;
                return 0;
            });

            let categoryCounts = {};
            lab_cal_scope_data_transaction.forEach(item => {
                if (!categoryCounts[item.category]) {
                    categoryCounts[item.category] = lab_cal_scope_data_transaction.filter(scope => scope.category === item.category).length;
                }
            });

            let renderedCategories = {}; // เก็บว่า category ไหนถูก render ไปแล้วหรือยัง
            

            let tableContent = '';
            if (lab_cal_scope_data_transaction && lab_cal_scope_data_transaction.length > 0) {
                tableContent = `
                <h5 class="text-primary">${labelText}</h5> 
                <table class="table table-bordered align-middle" id="cal_scope_table_${key}">
                    <thead class="bg-primary">
                        <tr>
                            <th class="text-center text-white">สาขาการสอบเทียบ</th>
                            <th class="text-center text-white">รายการสอบเทียบ</th>
                            <th class="text-center text-white">ค่าขีดความสามารถของ</th>
                            <th class="text-center text-white">วิธีการที่ใช้</th>
                            ${!hidden_delete_button ? '<th class="text-center text-white">ลบ</th>' : ''}
                        </tr>
                    </thead>
                    <tbody>
            `;
            }

            lab_cal_scope_data_transaction.forEach((item, index) => {
                let isFirstOccurrence = !renderedCategories[item.category];
                if (isFirstOccurrence) {
                    renderedCategories[item.category] = true;
                }

                // console.log(item)
            
                let formattedDescription = item.description
                    .replace(/ /g, '&nbsp;')
                    .replace(/\t/g, '&emsp;')
                    .replace(/\n/g, '<br>');
                tableContent += `
                    <tr>
                        ${isFirstOccurrence ? `<td rowspan="${categoryCounts[item.category]}" style="vertical-align: top;"><span style="font-weight:600"> สาขา${item.category_th}</span></td>` : ''}
                        <td>
                            <div>${item.instrument_text}</div>
                            <div style="margin-left: 15px;">
                                ${formattedDescription ? `<div style="white-space: pre-wrap;">${formattedDescription}</div>` : ''}
                                <div style="${formattedDescription ? 'margin-left: 15px;' : ''}">
                                    ${item.measurements.map(measurement => `
                                        <div>${measurement.name}</div>
                                        <div style="margin-left: 15px;">
                                            ${measurement.ranges.map(range => {
                                                let formattedRangeDescription = range.description
                                                    .replace(/ /g, '&nbsp;')
                                                    .replace(/\t/g, '&emsp;')
                                                    .replace(/\n/g, '<br>');
                                                return `
                                                    <div>${formattedRangeDescription || ''}</div>
                                                    ${/\.(png|jpg|jpeg|gif)$/i.test(range.range) ? 
                                                        `<img src="${range.range}" alt="Image" style="width: 160px;" />` : 
                                                        `<div style="margin-left: 15px;">
                                                            ${range.range ? range.range.split('\n').map(line => `<div>${line}</div>`).join('') : ''}
                                                        </div>`
                                                    }
                                                `;
                                            }).join('')}
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="visibility: hidden;">${item.instrument_text}</div>
                            <div style="margin-left: 15px;">
                                ${formattedDescription ? `<div style="visibility: hidden;white-space: pre-wrap;">${formattedDescription}</div>` : ''}
                                <div style="${formattedDescription ? 'margin-left: 15px;' : ''}">
                                    ${item.measurements.map(measurement => `
                                        <div style="visibility: hidden;">${measurement.name}</div>
                                        <div style="margin-left: 15px;">
                                            ${measurement.ranges.map(range => {
                                                let formattedRangeDescription = range.description
                                                    .replace(/ /g, '&nbsp;')
                                                    .replace(/\t/g, '&emsp;')
                                                    .replace(/\n/g, '<br>');
                                                return `
                                                    <div style="visibility: hidden">${formattedRangeDescription || ''}</div>
                                                    ${/\.(png|jpg|jpeg|gif)$/i.test(range.uncertainty) ? 
                                                        `<img src="${range.uncertainty}" alt="Image" style="width: 160px;" />` : 
                                                        `<div style="margin-left: 15px;">
                                                            ${range.uncertainty ? range.uncertainty.split('\n').map(line => `<div>${line}</div>`).join('') : ''}
                                                        </div>`
                                                    }
                                                `;
                                            }).join('')}
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="visibility: hidden;">${item.instrument_text}</div>
                            <div style="margin-left: 15px;">
                                ${item.measurements.map(measurement => `
                                    <div>${item.standard ? item.standard.split('\n').map(line => `<div>${line}</div>`).join('') : ''}</div>
                                `).join('')}
                            </div>
                        </td>

                        ${!hidden_delete_button ? `<td class="text-center">
                            <button class="btn btn-danger btn-sm" onclick="removeMainRow(${index}, '${key}')">ลบ</button>
                        </td>` : ''}
                    </tr>
                `;
            });
            
            tableContent += `
                    </tbody>
                </table>
            `;
            // เพิ่ม HTML ของตารางลงใน wrapper (ใช้ append แทน html)
            wrapper.append(tableContent);
        }
    }

    if (Array.isArray(lab_addresses_array)) {
        for (let i = 0; i < lab_addresses_array.length; i++) {
            const branchLabType = lab_addresses_array[i].lab_types;
        
            // สร้าง header สำหรับแต่ละสาขา (แสดงครั้งเดียว)
            let header = `<h5 class="text-primary">สาขา${lab_addresses_array[i].address_district_add_modal}</h5>`;
            let tableWrapper = ""; // เก็บเนื้อหาของตารางทั้งหมดในแต่ละสาขา
            for (let j = 1; j <= 5; j++) { // วนลูปตั้งแต่ 1 ถึง 5
                let key = `pl_2_${j}_branch`; // สร้างชื่อ key เช่น pl_2_1_main, pl_2_2_main
        
                if (Array.isArray(branchLabType[key])) { // ตรวจสอบว่าค่าเป็น array หรือไม่
                    // console.log(`Key ที่เป็น array: ${key}`); // แสดง key ที่เป็น array
                    // console.log(`ค่าของ ${key}:`, branchLabType[key]); // ดึงค่าของคีย์ที่เป็น array ออกมา
        
                    let facilityType = branchFacilityTypes.find(type => type.id === key); // ค้นหา label จาก mainFacilityTypes
                    let labelText = facilityType ? facilityType.text : "ไม่ทราบประเภท"; // ตรวจสอบว่าเจอหรือไม่
        
                    let lab_cal_scope_data_transaction = branchLabType[key];
                    // สร้างตัวแปรสำหรับนับจำนวน category
    
    
                    lab_cal_scope_data_transaction.sort((a, b) => {
                        if (a.category < b.category) return -1;
                        if (a.category > b.category) return 1;
                        return 0;
                    });
    
                    let categoryCounts = {};
                    lab_cal_scope_data_transaction.forEach(item => {
                        if (!categoryCounts[item.category]) {
                            categoryCounts[item.category] = lab_cal_scope_data_transaction.filter(scope => scope.category === item.category).length;
                        }
                    });
        
                    let renderedCategories = {}; // เก็บว่า category ไหนถูก render ไปแล้วหรือยัง
                    let tableContent = '';
                    if (lab_cal_scope_data_transaction && lab_cal_scope_data_transaction.length > 0) {
                        tableContent = `
                        <h5 class="text-primary">${labelText}</h5> 
                        <table class="table table-bordered align-middle" id="cal_scope_table_${key}">
                            <thead class="bg-primary">
                                <tr>
                                    <th class="text-center text-white">สาขาการสอบเทียบ</th>
                                    <th class="text-center text-white">รายการสอบเทียบ</th>
                                    <th class="text-center text-white">ค่าขีดความสามารถของ</th>
                                    <th class="text-center text-white">วิธีการที่ใช้</th>
                                    
                                     ${!hidden_delete_button ? '<th class="text-center text-white">ลบ</th>' : ''}
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    }
        
                    lab_cal_scope_data_transaction.forEach((item, index) => {
                        let isFirstOccurrence = !renderedCategories[item.category];
                        if (isFirstOccurrence) {
                            renderedCategories[item.category] = true;
                        }
        
                        console.log(item)
                    
                        let formattedDescription = item.description
                            .replace(/ /g, '&nbsp;')
                            .replace(/\t/g, '&emsp;')
                            .replace(/\n/g, '<br>');
                        tableContent += `
                            <tr>
                                ${isFirstOccurrence ? `<td rowspan="${categoryCounts[item.category]}" style="vertical-align: top;"><span style="font-weight:600"> สาขา${item.category_th}</span></td>` : ''}
                                <td>
                                    <div>${item.instrument_text}</div>
                                    <div style="margin-left: 15px;">
                                        ${formattedDescription ? `<div style="white-space: pre-wrap;">${formattedDescription}</div>` : ''}
                                        <div style="${formattedDescription ? 'margin-left: 15px;' : ''}">
                                            ${item.measurements.map(measurement => `
                                                <div>${measurement.name}</div>
                                                <div style="margin-left: 15px;">
                                                    ${measurement.ranges.map(range => {
                                                        let formattedRangeDescription = range.description
                                                            .replace(/ /g, '&nbsp;')
                                                            .replace(/\t/g, '&emsp;')
                                                            .replace(/\n/g, '<br>');
                                                        return `
                                                            <div>${formattedRangeDescription || ''}</div>
                                                            ${/\.(png|jpg|jpeg|gif)$/i.test(range.range) ? 
                                                                `<img src="${range.range}" alt="Image" style="width: 160px;" />` : 
                                                                `<div style="margin-left: 15px;">
                                                                    ${range.range ? range.range.split('\n').map(line => `<div>${line}</div>`).join('') : ''}
                                                                </div>`
                                                            }
                                                        `;
                                                    }).join('')}
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="visibility: hidden;">${item.instrument_text}</div>
                                    <div style="margin-left: 15px;">
                                        ${formattedDescription ? `<div style="visibility: hidden;white-space: pre-wrap;">${formattedDescription}</div>` : ''}
                                        <div style="${formattedDescription ? 'margin-left: 15px;' : ''}">
                                            ${item.measurements.map(measurement => `
                                                <div style="visibility: hidden;">${measurement.name}</div>
                                                <div style="margin-left: 15px;">
                                                    ${measurement.ranges.map(range => {
                                                        let formattedRangeDescription = range.description
                                                            .replace(/ /g, '&nbsp;')
                                                            .replace(/\t/g, '&emsp;')
                                                            .replace(/\n/g, '<br>');
                                                        return `
                                                            <div style="visibility: hidden">${formattedRangeDescription || ''}</div>
                                                            ${/\.(png|jpg|jpeg|gif)$/i.test(range.uncertainty) ? 
                                                                `<img src="${range.uncertainty}" alt="Image" style="width: 160px;" />` : 
                                                                `<div style="margin-left: 15px;">
                                                                    ${range.uncertainty ? range.uncertainty.split('\n').map(line => `<div>${line}</div>`).join('') : ''}
                                                                </div>`
                                                            }
                                                        `;
                                                    }).join('')}
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="visibility: hidden;">${item.instrument_text}</div>
                                    <div style="margin-left: 15px;">
                                        ${item.measurements.map(measurement => `
                                            <div>${item.standard ? item.standard.split('\n').map(line => `<div>${line}</div>`).join('') : ''}</div>
                                        `).join('')}
                                    </div>
                                </td>
                    
                                ${!hidden_delete_button ? `<td class="text-center">
                                    <button class="btn btn-danger btn-sm" onclick="removeBranchRow(${index}, '${key}', ${i})">ลบ</button>
                                </td>` : ''}

                            </tr>
                        `;
                    });
                    
                    tableContent += `
                            </tbody>
                        </table>
                    `;
                    // เพิ่ม HTML ของตารางลงใน wrapper (ใช้ append แทน html)
                    wrapper.append(header + tableContent);
                    // wrapper.html('sdfsdf');
                    // console.log(tableContent)
                }
            }
    
        }
    }
   
}

function renderTestScopeWithParameterTable(_wrapper,hidden_delete_button=true)  {

    lab_main_address = JSON.parse(sessionStorage.getItem('lab_main_address')) || [];
    lab_addresses_array = JSON.parse(sessionStorage.getItem('lab_addresses_array')) || [];

    // console.log('aha',lab_main_address)

    let wrapper = $(_wrapper);
    wrapper.empty(); 

    let labTypes = lab_main_address.lab_types; 

    for (let i = 1; i <= 5; i++) { 
        let key = `pl_2_${i}_main`; 

        if (Array.isArray(labTypes[key])) { 
            console.log(`Key สำนักงานใหญ่ ที่เป็น array: ${key}`); 
            console.log(`ค่าของ ${key}:`, labTypes[key]); 

            let facilityType = mainFacilityTypes.find(type => type.id === key); 
            let labelText = facilityType ? facilityType.text : "ไม่ทราบประเภท"; 


            let lab_test_scope_data_transaction = labTypes[key];

            lab_test_scope_data_transaction.sort((a, b) => {
                if (a.category < b.category) return -1;
                if (a.category > b.category) return 1;
                return 0;
            });

            let categoryCounts = {};
            lab_test_scope_data_transaction.forEach(item => {
                if (!categoryCounts[item.category]) {
                    categoryCounts[item.category] = lab_test_scope_data_transaction.filter(scope => scope.category === item.category).length;
                }
            });

                    // จัดเรียงข้อมูลตาม category


            let renderedCategories = {}; 
            let previousCategory = null; // ตัวแปรเก็บค่า category ก่อนหน้า

            let tableContent = '';
            if (lab_test_scope_data_transaction && lab_test_scope_data_transaction.length > 0) {
               
                tableContent = `
                    <h5 class="text-primary">${labelText}</h5> 
                    <table class="table custom-bordered-table table-no-hover" id="test_scope_table_${key}">
                        <thead class="bg-primary">
                            <tr>
                                <th class="text-center text-white">สาขาการทดสอบ</th>
                                <th class="text-center text-white">รายการทดสอบ</th>
                                <th class="text-center text-white">วิธีการที่ใช้</th>
                                ${!hidden_delete_button ? '<th class="text-center text-white">ลบ</th>' : ''}
                            </tr>
                        </thead>
                        <tbody>
                `;
            }
           

            lab_test_scope_data_transaction.forEach((item, index) => {
                // ตรวจสอบว่า category ซ้ำกับค่าก่อนหน้าหรือไม่
                let isCategoryHidden = item.category === previousCategory;
            
                // อัปเดตค่า category ก่อนหน้า
                previousCategory = item.category;
            
                if (!isCategoryHidden) {
                    // หาก category ยังไม่ซ้ำ
                    tableContent += `
                        <tr>
                            <td style="">
                                <div style="font-weight:600">
                                    สาขา${item.category_th}

                                </div>   
                            </td>
                            <td style="">
                                <div style="visibility: hidden;">
                                    สาขา${item.category_th}
                                    <br>
                                    <span>(${item.category} field)</span>
                                </div> 
                            </td>
                            <td style="">
                                <div style="visibility: hidden;">
                                    สาขา${item.category_th}
                                    <br>
                                    <span>(${item.category} field)</span>
                                </div>
                            </td>
                              <td class="text-center">
                               
                            </td>
                        </tr>
                    `;
                }
            
                // ส่วนที่แสดงทุกครั้ง (test_field, measurements, standard)
                tableContent += `
                    <tr>
                        <td style="vertical-align: top;padding:5px;padding-left:10px;">
                            <div>
                                <span style="margin-left:10px">${item.test_field}</span>
                            </div>

                        </td>
                        <td style="vertical-align: top;padding:5px;">
                            <div>${item.measurements[0].name}</div>
                            <div>(${item.measurements[0].name_eng})</div>
                            ${item.measurements[0].detail ? `<div style="padding-left: 10px;">${item.measurements[0].detail}</div>` : ''}
                        </td>
                        <td style="vertical-align: top;padding:5px;">
                            <div>
                                <span>${item.standard}</span>
                            </div>
                        </td>

                            ${!hidden_delete_button ? `<td class="text-center">
                                <button class="btn btn-danger btn-sm" onclick="removeMainRow(${index}, '${key}')">ลบ</button>
                            </td>` : ''}
                    </tr>
                `;
            });
            
            
            tableContent += `
                    </tbody>
                </table>
            `;

            wrapper.append(tableContent);

        }
    }

    if (Array.isArray(lab_addresses_array)) {
        for (let i = 0; i < lab_addresses_array.length; i++) {
            const branchLabType = lab_addresses_array[i].lab_types;
        
            // สร้าง header สำหรับแต่ละสาขา (แสดงครั้งเดียว)
            let header = `<h5 class="text-primary">สาขา${lab_addresses_array[i].address_district_add_modal}</h5>`;
            let tableWrapper = ""; // เก็บเนื้อหาของตารางทั้งหมดในแต่ละสาขา
        
            for (let j = 1; j <= 5; j++) { 
                let key = `pl_2_${j}_branch`; 
        
                if (Array.isArray(branchLabType[key])) { 
                    console.log(`Key สาขา ที่เป็น array: ${key}`); 
                    console.log(`ค่าของ ${key}:`, branchLabType[key]); 
        
                    let facilityType = branchFacilityTypes.find(type => type.id === key); 
                    let labelText = facilityType ? facilityType.text : "ไม่ทราบประเภท"; 
        
                    let lab_test_scope_data_transaction = branchLabType[key];
        
                    lab_test_scope_data_transaction.sort((a, b) => {
                        if (a.category < b.category) return -1;
                        if (a.category > b.category) return 1;
                        return 0;
                    });
    
                    let categoryCounts = {};
                    lab_test_scope_data_transaction.forEach(item => {
                        if (!categoryCounts[item.category]) {
                            categoryCounts[item.category] = lab_test_scope_data_transaction.filter(scope => scope.category === item.category).length;
                        }
                    });


                    let tableContent = '';
                    if (lab_test_scope_data_transaction && lab_test_scope_data_transaction.length > 0) {
                        tableContent = `
                        <h5 class="text-primary">${labelText}</h5> 
                        <table class="table custom-bordered-table table-no-hover" id="test_scope_table_${key}">
                            <thead class="bg-primary">
                                <tr>
                                    <th class="text-center text-white">สาขาการทดสอบ</th>
                                    <th class="text-center text-white">รายการทดสอบ</th>
                                    <th class="text-center text-white">วิธีการที่ใช้</th>
                                    ${!hidden_delete_button ? '<th class="text-center text-white">ลบ</th>' : ''}
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    }
        
                    
                    let previousCategory = null; // ตัวแปรเก็บค่า category ก่อนหน้า
                    lab_test_scope_data_transaction.forEach((item, index) => {
                        let isCategoryHidden = item.category === previousCategory;
                        previousCategory = item.category;
        
                        if (!isCategoryHidden) {
                            tableContent += `
                                <tr>
                                    <td>
                                        <div style="font-weight:600">
                                            สาขา${item.category_th}
                                        </div>
                                    </td>
                                    <td><div style="visibility: hidden;"></div></td>
                                    <td><div style="visibility: hidden;"></div></td>
                                    <td class="text-center"></td>
                                </tr>
                            `;
                        }
        
                        tableContent += `
                            <tr>
                                <td style="vertical-align: top;padding:5px;padding-left:10px;">
                                    <div>
                                        <span style="margin-left:10px">${item.test_field}</span>
                                    </div>
                                </td>
                                <td style="vertical-align: top;padding:5px;">
                                    <div>${item.measurements[0].name}</div>
                                    <div>(${item.measurements[0].name_eng})</div>
                                    ${item.measurements[0].detail ? `<div style="padding-left: 10px;">${item.measurements[0].detail}</div>` : ''}
                                </td>
                                <td style="vertical-align: top;padding:5px;">
                                    <div>
                                        <span>${item.standard}</span>
                                    </div>
                                </td>

                                  ${!hidden_delete_button ? `<td class="text-center">
                                    <button class="btn btn-danger btn-sm" onclick="removeBranchRow(${index}, '${key}', ${i})">ลบ</button>
                                </td>` : ''}
                            </tr>
                        `;
                    });
        
                    tableContent += `
                            </tbody>
                        </table>
                    `;
        
                    tableWrapper += tableContent;
                }
            }
            // เพิ่ม header และ tableWrapper ใน wrapper ทีเดียว
            wrapper.append(header + tableWrapper);
        }
    }
 
};


$(document).ready(function () {
    createDataFormat()
    if (labRequestType == 'cal'){
        renderCalScopeWithParameterTable('#scope_table_wrapper',true) 
    }else if(labRequestType == 'test')
    {
        renderTestScopeWithParameterTable('#scope_table_wrapper',true) 
    }
    
});

