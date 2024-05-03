<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import Highcharts1 from "highcharts";
import Highcharts2 from "highcharts";
import Highcharts3 from "highcharts";
import Loading from "../components/Loading.vue";

const items = ref([]);
const departments = ref([]);
const selectedDepartment = ref(null);
const users = ref([]);
const printer24 = ref([]);
const printer25 = ref([]);
const selectedUsers = ref(null);
const isLoading = ref(false);
const startDate = ref("");
const endDate = ref("");
const resultData = ref([]);

//select in chart
const selectedPrintersDep = ref(["Fuji24", "Fuji25"]);
const selectedPrintersUser = ref(["Fuji24", "Fuji25"]);
const selectedColors = ref(["total_color", "total_bw"]);

const formatNumber = (num) => {
    return num.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
};

onMounted(async () => {
    const currentDate = new Date();
    const firstDayOfMonth = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth(),
        1
    );
    const lastDayOfMonth = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth() + 1,
        0
    );

    startDate.value = formatDate(firstDayOfMonth);
    endDate.value = formatDate(currentDate);

    await fetchDataAndRenderChart1();
    await fetchDataAndRenderChart2();
    await fetchDataAndRenderChart3();

    // console.log(startDate.value);

    //data data all to table
    try {
        isLoading.value = true;
        const response = await axios.get("/api/report/data", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
            },
        });
        //console.log(response);
        isLoading.value = false;
        items.value = response.data.data;
        //console.log(items.value);
    } catch (error) {
        console.log(error);
    }

    //get department to select
    try {
        isLoading.value = true;
        const response = await axios.get("/api/report/data/department", {});
        //console.log(response);
        //isLoading.value = false;
        departments.value = response.data.data;
        //console.log(items.value);
    } catch (error) {
        console.log(error);
    }

    //get user to select
    try {
        isLoading.value = true;
        const response = await axios.get("/api/report/data/user", {});
        //console.log(response);
        isLoading.value = false;
        users.value = response.data.data;
        //console.log(items.value);
    } catch (error) {
        console.log(error);
    }

    //get printer
    try {
        isLoading.value = true;
        const response = await axios.get("/api/report/data/printer", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
            },
        });
        //console.log(response);
        isLoading.value = false;
        response.data.data.forEach((item) => {
            if (item.printername === "Fuji24") {
                printer24.value = item;
            } else if (item.printername === "Fuji25") {
                printer25.value = item;
            }
            // เพิ่มเงื่อนไขตามชื่อเครื่องพิมพ์อื่นๆ ตามต้องการ
        });
    } catch (error) {
        console.log(error);
    }
});

function formatDate(date) {
    const year = date.getFullYear();
    let month = date.getMonth() + 1;
    if (month < 10) {
        month = "0" + month;
    }
    let day = date.getDate();
    if (day < 10) {
        day = "0" + day;
    }
    return `${year}-${month}-${day}`;
}

function updateStartDate(value) {
    startDate.value = value;
}

function updateEndDate(value) {
    endDate.value = value;
}

async function fetchDataAndRenderChart1() {
    try {
        const response = await axios.get("/api/report/data/chartpie", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
                colors: selectedColors.value,
            },
        });
        const data = response.data.data;
        //console.log(data);
        renderChart(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }
}

function renderChart(data) {
    if (Highcharts1.value) {
        Highcharts1.value.destroy();
    }
    Highcharts1.chart("chart-pie-container", {
        chart: {
            type: "pie",
        },
        tooltip: {
            valueSuffix: "แผ่น",
        },
        title: {
            text: "",
        },
        plotOptions: {
            series: {
                allowPointSelect: true,
                cursor: "pointer",
                dataLabels: [
                    {
                        enabled: true,
                        distance: 20,
                    },
                    {
                        enabled: true,
                        distance: -40,
                        format: "{point.percentage:.1f}%",
                        style: {
                            fontSize: "1.2em",
                            textOutline: "none",
                            opacity: 0.7,
                        },
                        // filter: {
                        //     operator: ">",
                        //     property: "percentage",
                        //     value: 10,
                        // },
                    },
                ],
            },
        },
        series: [
            {
                name: "Percentage",
                colorByPoint: true,
                data: data.map((item) => ({
                    name: item.printer,
                    y: parseInt(item.total),
                })),
            },
        ],
    });
}

async function fetchDataAndRenderChart2() {
    try {
        const response = await axios.get("/api/report/data/chart/dep", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
                printers: selectedPrintersDep.value,
            },
        });
        const data = response.data.data;
        renderChart2(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }
}

function renderChart2(data) {
    if (Highcharts2.value) {
        Highcharts2.value.destroy();
    }

    // เรียงลำดับข้อมูลตาม total
    const sortedData = data.slice().sort((a, b) => b.total - a.total);

    const sortedDepartmentNames = sortedData.map(
        (item) => item.department_name
    );
    const sortedTotalColors = sortedData.map((item) =>
        parseInt(item.total_color)
    );
    const sortedTotalBW = sortedData.map((item) => parseInt(item.total_bw));
    const sortedTotal = sortedData.map((item) => parseInt(item.total));

    Highcharts2.chart("chart-container2", {
        chart: {
            type: "column",
        },
        title: {
            text: "",
        },
        // xAxis: {
        //     categories: sortedDepartmentNames,
        // },
        // yAxis: {
        //     min: 0,
        //     title: {
        //         text: "Total Usage",
        //     },
        // },
        // plotOptions: {
        //     series: {
        //         borderWidth: 0,
        //         dataLabels: {
        //             enabled: true,
        //             format: "{point.y}",
        //         },
        //     },
        // },
        xAxis: {
            categories: sortedDepartmentNames,
        },
        yAxis: {
            min: 0,
            title: {
                text: "normal",
            },
        },
        tooltip: {
            pointFormat:
                '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
            shared: true,
        },
        plotOptions: {
            column: {
                stacking: "normal",
                dataLabels: {
                    enabled: true,
                    // format: '{point.percentage:.0f}%'
                },
            },
        },

        series: [
            {
                name: "Total Color",
                data: sortedTotalColors,
            },
            {
                name: "Total BW",
                data: sortedTotalBW,
            },
            {
                name: "Total",
                data: sortedTotal,
            },
        ],
        exporting: {
            enabled: true,
        },
    });
}

async function fetchDataAndRenderChart3() {
    try {
        const response = await axios.get("/api/report/data/chart/users", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
                printers: selectedPrintersUser.value,
            },
        });
        const data = response.data.data;
        renderChart3(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }
}

function renderChart3(data) {
    if (Highcharts3.value) {
        Highcharts3.value.destroy();
    }

    // เรียงลำดับข้อมูลตาม total
    const sortedData = data.slice().sort((a, b) => b.total - a.total);

    const UserName = sortedData.map((item) => item.user);
    const totalColors = sortedData.map((item) => parseInt(item.total_color));
    const totalBW = sortedData.map((item) => parseInt(item.total_bw));
    const total = sortedData.map((item) => parseInt(item.total));

    // const UserName = data.map((item) => item.user);
    // const totalColors = data.map((item) => parseInt(item.total_color));
    // const totalBW = data.map((item) => parseInt(item.total_bw));
    // const total = data.map((item) => parseInt(item.total));

    Highcharts3.chart("chart-bar-user-container", {
        chart: {
            type: "column",
        },
        title: {
            text: "",
        },
        xAxis: {
            categories: UserName,
        },
        // yAxis: {
        //     min: 0,
        //     title: {
        //         text: "Total Usage",
        //     },
        // },
        yAxis: {
            min: 0,
            title: {
                text: "normal",
            },
        },
        tooltip: {
            pointFormat:
                '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
            shared: true,
        },
        plotOptions: {
            column: {
                stacking: "normal",
                dataLabels: {
                    enabled: true,
                    // format: '{point.percentage:.0f}%'
                },
            },
        },
        series: [
            {
                name: "Total Color",
                data: totalColors,
            },
            {
                name: "Total BW",
                data: totalBW,
            },
            {
                name: "Total",
                data: total,
            },
        ],
        exporting: {
            enabled: true,
        },
    });
}

async function filterDataPie() {
    await fetchDataAndRenderChart1();
}

async function filterDataBarDep() {
    await fetchDataAndRenderChart2();
}

async function filterDataBarUser() {
    await fetchDataAndRenderChart3();
}
async function submitForm() {
    try {

            Swal.fire({
                icon: "success",
                title: "Success",
                text: "Data loaded successfully.",
                showConfirmButton: false,
                timer: 1500,
            });

    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message, // แสดงข้อความ error ที่เกิดขึ้น
        });
    }
    try {
        const response = await axios.get("/api/report/data", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
                code: selectedUsers.value,
            },
        });
        items.value = response.data.data;
    } catch (error) {
        console.log(error);
    }
    try {
        const response = await axios.get("/api/report/data/chartpie", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
                colors: selectedColors.value,
                code: selectedUsers.value,
            },
        });
        const data = response.data.data;
        //console.log(data);
        renderChart(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }
    try {
        const response = await axios.get("/api/report/data/chartpie", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
                colors: selectedColors.value,
                code: selectedUsers.value,
            },
        });
        const data = response.data.data;
        //console.log(data);
        renderChart(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }

    try {
        const response = await axios.get("/api/report/data/chart/dep", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
                printers: selectedPrintersDep.value,
                code: selectedUsers.value,
            },
        });
        const data = response.data.data;
        renderChart2(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }

    try {
        const response = await axios.get("/api/report/data/chart/users", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
                printers: selectedPrintersUser.value,
                code: selectedUsers.value,
            },
        });
        const data = response.data.data;
        renderChart3(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }

    //get printer
    try {
        isLoading.value = true;
        const response = await axios.get("/api/report/data/printer", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
                code: selectedUsers.value,
            },
        });
        //console.log(response);
        isLoading.value = false;
        response.data.data.forEach((item) => {
            if (item.printername === "Fuji24") {
                printer24.value = item;
            } else if (item.printername === "Fuji25") {
                printer25.value = item;
            }
        });
    } catch (error) {
        console.log(error);
    }
}
</script>

<template>
    <div class="mt-3">
        <div class="row">

            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form class="row g-3" @submit.prevent="submitForm">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label"
                                    >วันเริ่มต้น</label
                                >
                                <input
                                    type="date"
                                    class="form-control"
                                    id="startDate"
                                    :value="startDate"
                                    @input="
                                        updateStartDate($event.target.value)
                                    "
                                />
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label"
                                    >วันสิ้นสุุด</label
                                >
                                <input
                                    type="date"
                                    class="form-control"
                                    id="endDate"
                                    :value="endDate"
                                    @input="updateEndDate($event.target.value)"
                                />
                            </div>
                            <!-- <div class="col-md-6">
                                <label for="" class="form-label"
                                    >เลือกแผนก</label
                                >
                                <select
                                    class="form-select"
                                    id="department"
                                    name="department"
                                    aria-label="Default select example"
                                    v-model="selectedDepartment"
                                    multiple
                                >
                                    <option value="">เลือก</option>
                                    <option
                                        v-for="department in departments"
                                        :key="department.department_id"
                                        :value="department.department_id"
                                    >
                                        {{ department.department_name }}
                                    </option>
                                </select>
                            </div> -->
                            <div class="col-md-6">
                                <label for="" class="form-label"
                                    >เลือกผู้ใช้</label
                                >
                                <select
                                    class="form-select"
                                    id="user"
                                    name="user"
                                    v-model="selectedUsers"
                                    multiple
                                    aria-label="Default select example"
                                >
                                    <option value="">เลือก</option>
                                    <option
                                        v-for="user in users"
                                        :key="user.code"
                                        :value="user.code"
                                    >
                                        {{ user.user }}
                                    </option>
                                </select>
                            </div>
                            <div
                                class="cd-grid gap-2 d-md-flex justify-content-md-center"
                            >
                                <button
                                    type="submit"
                                    class="btn btn-primary mr-2"
                                >
                                    <i class="fas fa-search"></i> ตกลง
                                </button>
                                <a href="/report" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> ยกเลิก
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card l-bg-blue-dark">
                    <div class="card-statistic-3 p-4">
                        <div class="card-icon card-icon-large">
                            <i class="fas fa-print"></i>
                        </div>
                        <div class="mb-4">
                            <h4 class="card-title mb-0">
                                เครื่องพิมพ์ Fuji 24
                            </h4>
                        </div>
                        <div class="row align-items-center mb-2 d-flex">
                            <div class="col-8">
                                <h5 class="d-flex align-items-center mb-0">
                                    รวมทั้งหมด
                                </h5>
                            </div>
                            <div class="col-4 text-right">
                                <h5 class="d-flex align-items-center mb-0">
                                    <span>{{
                                        formatNumber(
                                            parseInt(printer24.total)
                                        ).toLocaleString()
                                    }}</span>
                                </h5>
                            </div>

                            <div class="col-8">
                                <h6 class="d-flex align-items-center mb-0">
                                    พิมพ์สี
                                </h6>
                            </div>
                            <div class="col-4 text-right">
                                <h5 class="d-flex align-items-center mb-0">
                                    <span>{{
                                        formatNumber(
                                            parseInt(printer24.total_color)
                                        ).toLocaleString()
                                    }}</span>
                                </h5>
                            </div>
                            <div class="col-8">
                                <h6 class="d-flex align-items-center mb-0">
                                    พิมพ์ขาวดำ
                                </h6>
                            </div>
                            <div class="col-4 text-right">
                                <h5 class="d-flex align-items- mb-0">
                                    <span>{{
                                        formatNumber(
                                            parseInt(printer24.total_bw)
                                        ).toLocaleString()
                                    }}</span>
                                </h5>
                            </div>
                        </div>
                        <!-- <div class="progress mt-1 " data-height="8" style="height: 8px;">
                        <div class="progress-bar l-bg-cyan" role="progressbar" data-width="25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%;"></div>
                    </div> -->
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card l-bg-blue-dark">
                    <div class="card-statistic-3 p-4">
                        <div class="card-icon card-icon-large">
                            <i class="fas fa-print"></i>
                        </div>
                        <div class="mb-4">
                            <h4 class="card-title mb-0">
                                เครื่องพิมพ์ Fuji 25
                            </h4>
                        </div>
                        <div class="row align-items-center mb-2 d-flex">
                            <div class="col-8">
                                <h5 class="d-flex align-items-center mb-0">
                                    รวมทั้งหมด
                                </h5>
                            </div>
                            <div class="col-4 text-right">
                                <h5 class="d-flex align-items-center mb-0">
                                    <span>{{
                                        formatNumber(
                                            parseInt(printer25.total)
                                        ).toLocaleString()
                                    }}</span>
                                </h5>
                            </div>

                            <div class="col-8">
                                <h6 class="d-flex align-items-center mb-0">
                                    พิมพ์สี
                                </h6>
                            </div>
                            <div class="col-4 text-right">
                                <h5 class="d-flex align-items-center mb-0">
                                    <span>{{
                                        formatNumber(
                                            parseInt(printer25.total_color)
                                        ).toLocaleString()
                                    }}</span>
                                </h5>
                            </div>
                            <div class="col-8">
                                <h6 class="d-flex align-items-center mb-0">
                                    พิมพ์ขาวดำ
                                </h6>
                            </div>
                            <div class="col-4 text-right">
                                <h5 class="d-flex align-items- mb-0">
                                    <span>{{
                                        formatNumber(
                                            parseInt(printer25.total_bw)
                                        ).toLocaleString()
                                    }}</span>
                                </h5>
                            </div>
                        </div>
                        <!-- <div class="progress mt-1 " data-height="8" style="height: 8px;">
                        <div class="progress-bar l-bg-cyan" role="progressbar" data-width="25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 25%;"></div>
                    </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">ปริมาณการพิมพ์แต่ละเครื่อง</h5>
                        <div class="form-check form-check-inline">
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="total_color"
                                    v-model="selectedColors"
                                    value="total_color"
                                    @change="filterDataPie"
                                />
                                <label class="form-check-label" for="color"
                                    >Total Color</label
                                >
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="total_bw"
                                    v-model="selectedColors"
                                    value="total_bw"
                                    @change="filterDataPie"
                                />
                                <label class="form-check-label" for="bw"
                                    >Total BW</label
                                >
                            </div>
                        </div>
                        <div
                            id="chart-pie-container"
                            style="
                                min-width: 310px;
                                height: 400px;
                                margin: 0 auto;
                            "
                        ></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">ปริมาณการใช้งานรวมแต่ละแผนก</h5>
                        <div class="form-check form-check-inline">
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="fuji24"
                                    v-model="selectedPrintersDep"
                                    value="Fuji24"
                                    @change="filterDataBarDep"
                                />
                                <label class="form-check-label" for="fuji24"
                                    >Fuji24</label
                                >
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="fuji25"
                                    v-model="selectedPrintersDep"
                                    value="Fuji25"
                                    @change="filterDataBarDep"
                                />
                                <label class="form-check-label" for="fuji25"
                                    >Fuji25</label
                                >
                            </div>
                        </div>
                        <div
                            id="chart-container2"
                            style="
                                min-width: 310px;
                                height: 400px;
                                margin: 0 auto;
                            "
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <div class="row">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">ปริมาณการพิมพ์ของผู้ใช้งาน</h5>
                        <div class="form-check form-check-inline">
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="fuji24"
                                    v-model="selectedPrintersUser"
                                    value="Fuji24"
                                    @change="filterDataBarUser"
                                />
                                <label class="form-check-label" for="fuji24"
                                    >Fuji24</label
                                >
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="fuji25"
                                    v-model="selectedPrintersUser"
                                    value="Fuji25"
                                    @change="filterDataBarUser"
                                />
                                <label class="form-check-label" for="fuji25"
                                    >Fuji25</label
                                >
                            </div>
                        </div>
                        <div
                            id="chart-bar-user-container"
                            style="
                                min-width: 310px;
                                height: 400px;
                                margin: 0 auto;
                            "
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info bg-gradient">
                        ตารางสรุปรายการใช้งานเครื่องพิมพ์
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">แผนก</th>
                                        <th scope="col">รหัสพนักงาน</th>
                                        <th scope="col">ชื่อ-สกุล</th>
                                        <th width="10%">สี</th>
                                        <th width="10%">ขาวดำ</th>
                                        <th width="10%">รวม</th>
                                    </tr>
                                </thead>
                                <tbody v-if="isLoading">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <loading></loading>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-if="!isLoading">
                                    <!-- แสดงข้อความ "No data available" ถ้าไม่มีข้อมูล -->
                                    <tr v-if="items.length === 0">
                                        <td colspan="7" class="text-center">
                                            No data available
                                        </td>
                                    </tr>
                                    <!-- วนลูปผ่านแต่ละแผนก -->
                                    <template
                                        v-for="(department, index) in items"
                                        :key="index"
                                    >
                                        <tr>
                                            <td
                                                colspan=""
                                                class="text-center"
                                                style="
                                                    background-color: #bfdbfe;
                                                "
                                            >
                                                {{ department.department_name }}
                                            </td>
                                            <td
                                                colspan="5"
                                                class="text-center"
                                                style="
                                                    background-color: #bfdbfe;
                                                "
                                            ></td>
                                        </tr>
                                        <!-- วนลูปผ่านผู้ใช้งานในแผนก -->
                                        <tr
                                            v-for="(
                                                user, userIndex
                                            ) in department.users"
                                            :key="'user-' + userIndex"
                                        >
                                            <td></td>
                                            <td>{{ user.code }}</td>
                                            <td>{{ user.name }}</td>
                                            <td class="text-center">
                                                {{ user.total_color }}
                                            </td>
                                            <td class="text-center">
                                                {{ user.total_bw }}
                                            </td>
                                            <td class="text-center">
                                                {{ user.total }}
                                            </td>
                                        </tr>
                                        <!-- แสดงผลรวมของแผนก -->
                                        <tr style="background-color: #93c5fd">
                                            <td colspan="3"></td>
                                            <!-- <td class="text-right">Total department</td> -->
                                            <td class="text-center">
                                                {{ department.total_color }}
                                            </td>
                                            <td class="text-center">
                                                {{ department.total_bw }}
                                            </td>
                                            <td class="text-center">
                                                {{ department.total }}
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
