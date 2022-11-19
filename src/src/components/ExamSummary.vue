<template>
    <div class="w-full">
        <div v-if="points >= POINTS_THRESHOLD" class="border-2 border-green-200 mx-1 p-2 text-center text-lg">ZDANY {{points}} / 74</div>
        <div v-if="points < POINTS_THRESHOLD" class="border-2 border-red-200 mx-1 p-2 text-center text-lg">NIEZDANY {{points}} / 74, min. {{POINTS_THRESHOLD}}</div>
        <div class="mx-1 p-2 text-lg">Link do wyników Twojego egzaminu</div>
        <div 
            class="mx-1 p-2 text-md"
            title="Kliknij aby skopiować do schowka"
            @click.prevent="copyToClipboard(ROOT_URL +'/result/' + props.result_id)">
            {{ROOT_URL}}/result/{{props.result_id}}
        </div>
        <div class="mx-1 p-2 text-lg">Czas egzaminu: {{formatTime(total_time)}} / max ok 25 min</div>
        <div class="mx-1 p-2 text-lg">Pytania i odpowiedzi:</div>
        <div class="mx-1">
            <div v-for="(a, idx) in props.answers" :key="a.question.id" class="mb-3">
                <div class="mb-1">{{idx + 1}}. {{a.question.q_PL}}</div>
                <div class="mb-2 border-2 p-2 border-gray-100 "
                    :class="{
                        'bg-green-400': a.question.a === a.answer,
                        'bg-red-400': a.question.a !== a.answer 
                    }">
                    <input type="radio"  checked>
                    <span class="ml-2">{{displayAnswer(a)}}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, defineEmits, onMounted } from 'vue'
import { ROOT_URL } from "@/stores/session.ts"
const props = defineProps<{
  answers: Array<object>,
  t_start: number,
  t_end: number,
  result_id: string
}>()

const emit = defineEmits(['passedExam'])

const POINTS_THRESHOLD = 68
const points = ref(0)
const total_time = ref(0)

onMounted(() => {
    props.answers.forEach(a => {
        if (a.question.a === a.answer) {
            points.value += parseInt(a.question.points)
        }
    })
    total_time.value = Math.round((props.t_end - props.t_start) / 1000)
})

watch(points, () => {
    emit("passedExam", points.value >= POINTS_THRESHOLD)
})

const copyToClipboard = (text : string) => {
    if (!navigator.clipboard) {
        const to_copy = document.createElement('textarea');
        document.body.appendChild(to_copy);
        to_copy.value = text;
        to_copy.select();
        to_copy.setSelectionRange(0, 99999); /*For mobile devices*/
        /* Copy the text inside the text field */
        document.execCommand("copy");
        to_copy.remove();
    } else {
        navigator.clipboard.writeText(text).then(
            function(){
                console.log('[copied]')
            })
            .catch(
                function(error) {
                    console.log('[error copy]', error)
            });
    }
}


const formatTime = (time: number) : string => {
    const hours = Math.floor(time / 60 / 60)
    const minutes = Math.floor(( time - (hours * 60 * 60) ) / 60)
    const seconds = time - (hours * 60 * 60) - (minutes * 60)
    let str = ''
    if (hours) {
        str += (hours).toLocaleString('pl-PL', {minimumIntegerDigits: 2, useGrouping:false}) + ':'
    }
    str += (minutes).toLocaleString('pl-PL', {minimumIntegerDigits: 2, useGrouping:false}) + ':'
    str += (seconds).toLocaleString('pl-PL', {minimumIntegerDigits: 2, useGrouping:false})
    return str
}

const displayAnswer = (answer : object) : string => {

    if (['N', 'T'].includes(answer.question.a)) {
        const mapping = { 'N': 'Nie', 'T': 'Tak', '': 'Brak odpowiedzi'}
        return mapping[answer.answer]
    } else {
        return answer.question['a_' + answer.answer + '_PL']
    }
    
}

</script>

<style lang="scss" scoped>

</style>