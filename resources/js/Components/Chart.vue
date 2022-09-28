<script setup>
import { Chart, registerables } from "chart.js";
import { BarChart } from "vue-chart-3";
import { reactive, computed } from "vue";   // computedで親コンポの日付が変わり次第更新をする役割

const props = defineProps({
    "data" : Object // Analysis.vueから【:data="data"】で渡ってきたデータを受け取り
})

const labels = computed(() => props.data.labels)
const totals = computed(() => props.data.totals)

Chart.register(...registerables);

const barData = reactive({
    labels: labels,
    datasets: [
        {
            label: '売上',
            data: totals,
            backgroundColor: "rgb(75, 192, 192)",
            tension: 0.1,
        }
    ]
})
</script>
<template>
<div v-show="props.data">
    <BarChart :chartData="barData" />
</div>
</template>