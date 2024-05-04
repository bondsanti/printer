<script setup>
import { ref, onMounted } from "vue";
import Highcharts3 from "highcharts";
import Highcharts4 from "highcharts";
import axios from "axios";

const selectedColors = ref(["total_color", "total_bw"]);
const selectedPrinters = ref(["Fuji24", "Fuji25"]);

onMounted(async () => {
    await Promise.all([fetchDataAndRenderChart()]);
    await Promise.all([fetchDataAndRenderChart2()]);
});

async function fetchDataAndRenderChart() {
    try {
        const response = await axios.get("/api/data/chartpie", {
            params: {
                colors: selectedColors.value, // ส่งเครื่องพิมพ์ที่เลือกไปให้ API
            },
        });
        const data = response.data.data;
        //console.log(data);
        renderChart(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }
}

async function fetchDataAndRenderChart2() {
    try {
        const response = await axios.get("/api/data/chartsimidonut", {
            params: {
                printers: selectedPrinters.value,
            },
        });
        const data = response.data.data;
        //console.log(data);
        renderChart2(data);
    } catch (error) {
        console.log("Error fetching data:", error);
    }
}

function renderChart(data) {
    if (Highcharts3.value) {
        Highcharts3.value.destroy();
    }
    Highcharts3.chart("chart-pie-container", {
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

function renderChart2(data) {
    if (Highcharts4.value) {
        Highcharts4.value.destroy();
    }

    Highcharts4.chart("chart-simidonut-container", {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false,
        },
        title: {
            text: "TOP 10",
            align: "center",
            verticalAlign: "middle",
            y: 60,
            style: {
                fontSize: "1.1em",
            },
        },
        // tooltip: {
        //     pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>",
        // },
        tooltip: {
            pointFormat: 'Total : <b>{point.y} ({point.percentage:.1f}%)</b><br/>Total Color: <b>{point.total_color} </b><br/>Total BW: <b>{point.total_bw}</b>',
        },
        accessibility: {
            point: {
                valueSuffix: "%",
            },
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: 5,

                    style: {
                            textOutline: "none",
                            opacity: 0.7,

                    },
                },
                startAngle: -90,
                endAngle: 90,
                center: ["50%", "75%"],
                size: "95%",
            },
        },
        series: [
            {
                type: "pie",
                innerSize: "50%",
                data: data.map((item) => ({
                    name: item.user,
                    y: parseInt(item.total),
                    total_color: parseInt(item.total_color),
                    total_bw: parseInt(item.total_bw),
                    // total: parseInt(item.total),
                })),
            },
        ],
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
                <h5 class="card-title">ปริมาณการพิมพ์แต่ละเครื่อง</h5>
                <div class="form-check form-check-inline">
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="total_color"
                            v-model="selectedColors"
                            value="total_color"
                            @change="filterDataBar"
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
                            @change="filterDataBar"
                        />
                        <label class="form-check-label" for="bw"
                            >Total BW</label
                        >
                    </div>
                </div>
                <div
                    id="chart-pie-container"
                    style="min-width: 310px; height: 400px; margin: 0 auto"
                ></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">ปริมาณการพิมพ์ของผู้ใช้งาน TOP 10</h5>
                <div class="form-check form-check-inline">
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="fuji24"
                            v-model="selectedPrinters"
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
                            v-model="selectedPrinters"
                            value="Fuji25"
                            @change="filterDataBar2"
                        />
                        <label class="form-check-label" for="fuji25"
                            >Fuji25</label
                        >
                    </div>
                </div>
                <div
                    id="chart-simidonut-container"
                    style="min-width: 310px; height: 400px; margin: 0 auto"
                ></div>
            </div>
        </div>
    </div>
</template>


