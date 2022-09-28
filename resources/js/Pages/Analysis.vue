<script setup>
import { getToday } from '@/common';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import { onMounted, reactive } from 'vue';
import Chart from '@/Components/Chart.vue'
import ResultTable from '@/Components/ResultTable.vue'

// 検索日時の初期値は操作している日に設定 getToday()はオリジナル関数
onMounted(() => {
    form.startDate = getToday()
    form.endDate = getToday()
})

const form = reactive({
    startDate : null,
    endDate : null,
    type : 'perDay'
})

const data = reactive({})

const getDate = async () => {
    try{
        await axios.get('/api/analysis/', {
            params: {   // apiのポイントにパラメータを付けている
                startDate: form.startDate,
                endDate: form.endDate,
                type: form.type // 日別、月別など切り分けるフラグとして使うのかな
            }
        })
        .then( res => {
            data.data = res.data.data
            data.labels = res.data.labels
            data.totals = res.data.totals
            data.type = res.data.type
            console.log(res.data)
        })
    } catch(e){
        console.log(e.message)
    }
}

</script>

<template>
    <Head title="データ分析" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                データ分析
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="getDate"><!--  @submit.prevent="" → formが送信された際に処理を走らせる書き方   -->
                            分析方法<br>
                            <input type="radio" v-model="form.type" value="perDay" checked><span class="mr-2">日別</span>
                            <input type="radio" v-model="form.type" value="perMonth"><span class="mr-2">月別</span>
                            <input type="radio" v-model="form.type" value="perYear"><span class="mr-2">年別</span>
                            <input type="radio" v-model="form.type" value="decile"><span class="mr-2">デシル分析</span>
                            <br>
                            From: <input type="date" name="startDate" v-model="form.startDate">
                            To: <input type="date" name="endDate" v-model="form.endDate"><br>
                            <button class="mt-4 flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">分析する</button>
                        </form>

                        <div v-if="data.data">
                        <Chart :data="data"/><!-- 【getDate】でapiのレスポンス結果をChartコンポに渡す -->
                        <ResultTable :data="data" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
