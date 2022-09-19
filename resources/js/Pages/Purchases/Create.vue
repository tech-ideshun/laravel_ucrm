<script setup>
import { getToday } from '@/common';    // 拡張子のjsは不要
import { onMounted, reactive, ref, computed } from 'vue';

const props = defineProps({
    'customers' : Array,
    'items' : Array
})

const itemList = ref([])    // リアクティブな配列を準備（上記、propsのままだと値が変更できなかったので配列でDBを更新する予定）

onMounted(() => {
    form.date = getToday()

    props.items.forEach((item) => {
        itemList.value.push({   // 配列に1つずつ処理
            id: item.id,
            name: item.name,
            price: item.price,
            quantity: 0 // これは下記配列から数値が渡ってくるので、初期値は0で設定
        })
    })
})
const quantity = ["0","1", "2","3","4","5","6","7","8","9"] // optionタグ用


/* 【computed】はcomputedで囲っている箇所を常に監視しており、値が変更され次第再度処理を実行するライブラリ
※computedは必ずreturnが必須です※ */
const totalPrice = computed(() => {
    let total = 0
    itemList.value.forEach((item) => {
        total += item.price * item.quantity
    })
    return total
})

const form = reactive({
    date: null,
    customer_id: null
})

</script>

<template>
    日付<br>
    <input type="date" name="date" v-model="form.date">

    会員名<br>
    <select name="customer" v-model="form.customer_id">
        <option v-for="customer in customers" :value="customer.id" :key="customer.id">
            {{ customer.id }}:{{ customer.name }}
        </option>
    </select>
    <br><br>
    商品・サービス名<br>
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>商品名</th>
                <th>金額</th>
                <th>数量</th>
                <th>小計</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in itemList">
                <td>{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.price }}</td>
                <td>
                    <select name="quantity" v-model="item.quantity">
                        <option v-for="q in quantity" :value="q">{{ q }}</option>
                    </select>
                </td>
                <td>
                    {{ item.price * item.quantity }}
                </td>
            </tr>
        </tbody>
    </table>
    <br><br>
    合計: {{ totalPrice }} 円




</template>