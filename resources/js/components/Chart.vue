<script setup>
import { ref, onMounted } from "vue";
import Highcharts from "highcharts";
import Highcharts2 from "highcharts";
import axios from "axios";

const selectedPrinters = ref(["Fuji24", "Fuji25"]);
const selectedPrinters2 = ref(["Fuji24", "Fuji25"]);

const thaiMonths = {
    1: "มกราคม",
    2: "กุมภาพันธ์",
    3: "มีนาคม",
    4: "เมษายน",
    5: "พฤษภาคม",
    6: "มิถุนายน",
    7: "กรกฎาคม",
    8: "สิงหาคม",
    9: "กันยายน",
    10: "ตุลาคม",
    11: "พฤศจิกายน",
    12: "ธันวาคม",
};

onMounted(async () => {
    await fetchDataAndRenderChart();
    await fetchDataAndRenderChart2();
});

async function fetchDataAndRenderChart() {
    try {
        const response = await axios.get("/api/data/chart", {
            params: {
                printers: selectedPrinters.value,
            },
        });
        const data = response.data.data;
        renderChart(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }
}

async function fetchDataAndRenderChart2() {
    try {
        const response = await axios.get("/api/data/chart/dep", {
            params: {
                printers: selectedPrinters2.value,
            },
        });
        const data = response.data.data;
        renderChart2(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }
}

function renderChart(data) {
    if (Highcharts.value) {
        Highcharts.value.destroy();
    }
    const thaiMonthsLabels = data.map((item) => thaiMonths[item.month]);
    const totalColors = data.map((item) => parseInt(item.total_color));
    const totalBW = data.map((item) => parseInt(item.total_bw));
    const total = data.map((item) => parseInt(item.total));
    // console.log(thaiMonthsLabels);
    // console.log(totalColors);
    Highcharts.chart("chart-container", {
        chart: {
            type: "column",
        },
        title: {
            text: "",
        },
        xAxis: {
            categories: thaiMonthsLabels,
        },
        yAxis: {
            min: 0,
            title: {
                text: "Total Usage",
            },
        },
        plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y}'
                }
            }
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


function renderChart2(data) {
    if (Highcharts2.value) {
        Highcharts2.value.destroy();
    }

    // const departmentNames = data.map((item) => item.department_name);
    // const totalColors = data.map((item) => parseInt(item.total_color));
    // const totalBW = data.map((item) => parseInt(item.total_bw));
    // const total = data.map((item) => parseInt(item.total));

    // เรียงลำดับข้อมูลตาม total
    const sortedData = data.slice().sort((a, b) => b.total - a.total);

    const sortedDepartmentNames = sortedData.map((item) => item.department_name);
    const sortedTotalColors = sortedData.map((item) => parseInt(item.total_color));
    const sortedTotalBW = sortedData.map((item) => parseInt(item.total_bw));
    const sortedTotal = sortedData.map((item) => parseInt(item.total));

    Highcharts2.chart("chart-container2", {
        chart: {
            type: "column",
        },
        title: {
            text: "",
        },
        xAxis: {
            categories: sortedDepartmentNames,
        },
        // yAxis: {
        //     min: 0,
        //     title: {
        //         text: "Total Usage",
        //     },
        // },
        // plotOptions: {
        // series: {
        //     borderWidth: 0,
        //     dataLabels: {
        //         enabled: true,
        //         format: '{point.y}'
        //         }
        //     }
        // },
        yAxis: {
            min: 0,
            title: {
                text: "Total Usage",
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


async function filterDataBar() {
    await fetchDataAndRenderChart();
}

async function filterDataBar2() {
    await fetchDataAndRenderChart2();
}
</script>
<template>
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">ปริมาณการพิมพ์ของเครื่องในแต่ละเดือน</h5>
                <div class="form-check form-check-inline">
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="fuji24"
                            v-model="selectedPrinters"
                            value="Fuji24"
                            @change="filterDataBar"
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
                            v-model="selectedPrinters"
                            value="Fuji25"
                            @change="filterDataBar"
                        />
                        <label class="form-check-label" for="fuji25"
                            >Fuji25</label
                        >
                    </div>
                </div>
                <div
                    id="chart-container"
                    style="min-width: 310px; height: 400px; margin: 0 auto"
                ></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">ปริมาณการใช้งานรวมในแต่ละแผนก</h5>
                <div class="form-check form-check-inline">
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="fuji24"
                            v-model="selectedPrinters2"
                            value="Fuji24"
                            @change="filterDataBar2"
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
                            v-model="selectedPrinters2"
                            value="Fuji25"
                            @change="filterDataBar2"
                        />
                        <label class="form-check-label" for="fuji25"
                            >Fuji25</label
                        >
                    </div>
                </div>
                <div
                    id="chart-container2"
                    style="min-width: 310px; height: 400px; margin: 0 auto"
                ></div>
            </div>
        </div>
    </div>
</template>

