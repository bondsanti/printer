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
                <h5 class="card-title"></h5>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Highcharts3 from "highcharts";
import axios from "axios";

const selectedColors = ref(["total_color", "total_bw"]);



onMounted(async () => {
    await Promise.all([fetchDataAndRenderChart()]);
});

async function fetchDataAndRenderChart() {
    try {
        const response = await axios.get("/api/data/chartpie", {
            params: {
                colors: selectedColors.value, // ส่งเครื่องพิมพ์ที่เลือกไปให้ API
            },
        });
        const data = response.data.data;
        renderChart(data);
    } catch (error) {
        console.error("Error fetching data:", error);
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
            valueSuffix: "%",
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
                        filter: {
                            operator: ">",
                            property: "percentage",
                            value: 10,
                        },
                    },
                ],
            },
        },
        series: [
            {
                name: "Percentage",
                colorByPoint: true,
                data: data.map(item => ({
                    name: item.printer,
                    y: parseInt(item.total)
                }))
            },
        ],
    });
}

async function filterDataBar() {
    await fetchDataAndRenderChart();
}
</script>

