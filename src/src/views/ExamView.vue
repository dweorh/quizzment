<!-- eslint-disable prettier/prettier -->
<template>
    <div class="flex flex-col items-center w-full">
        <component :is="RouterLink" to="/lesson" class="self-start pl-2 m-2">Zakończ</component>
        <div v-if="!finished" class="flex flex-col items-center">
            <div class="flex mb-1 flex-wrap pl-1 lg:pl-0 w-full">
                <div
                    v-for="i in questions.length" 
                    :key="i" 
                    class="pr-1 pl-1 mr-1 border-2 border-gray-100"
                    :class="{'bg-zinc-400': i <= idx, 'bg-zinc-200' : i - 1 === idx}"
                > {{i}}
                </div>
            </div>
            <component
                :is="SingleQuestion"
                v-if="questions.length > 0"
                :question="questions[idx]"
                @got-answer="gotAnswer(questions[idx], idx, $event)"
                :key="idx"
            />
        </div>
        <div v-if="finished" class="flex flex-col items-center w-full">
            <div class="flex cursor-pointer mb-1 flex-wrap pl-1 lg:pl-0 mx-1">
                <div
                    v-for="i in answers.length" 
                    :key="i" 
                    class="pr-1 pl-1 mr-1 border-2"
                    :class="{
                        'bg-green-400': answers[i-1].question.a === answers[i-1].answer && (a_idx !== i - 1 || display_summary),
                        'bg-red-400' : answers[i-1].question.a !== answers[i-1].answer && (a_idx !== i - 1 || display_summary),
                        'text-green-400 border-green-400 bg-white': answers[i-1].question.a === answers[i-1].answer && (a_idx === i - 1 &&!display_summary),
                        'text-red-400 border-red-400 bg-white' : answers[i-1].question.a !== answers[i-1].answer && (a_idx === i - 1 && !display_summary)
                    }"
                    @click="showAnswer(i-1)"
                > {{i}}
                </div>
                <div
                    class="pr-1 pl-1 border-2"
                    :class="{
                        'bg-red-400': !passed,
                        'bg-green-400': passed
                    }"
                    @click="showAnswer('summary')">
                    Wynik
                    </div>
            </div>
            <component
                v-if="!display_summary"
                :is="SingleAnswer"
                :answer="answers[a_idx]"
                :key="a_idx"
            />
            <component
                v-if="display_summary"
                :is="ExamSummary"
                :answers="answers"
                :t_start="t_start"
                :t_end="t_end"
                :key="examId"
                :result_id="exam_result_id"
                 @passedExam="passed = $event"
                 />
            <!-- <div v-if="points >= POINTS_THRESHOLD" class="border-2 border-green-200 w-full p-2 text-center">ZDANY {{points}} / 74</div>
            <div v-if="points < POINTS_THRESHOLD" class="border-2 border-red-200 w-full p-2 text-center">NIEZDANY {{points}} / 74, min. {{POINTS_THRESHOLD}}</div> -->
        </div>
    </div>
</template>

<script setup lang="ts">
import { RouterLink } from 'vue-router'
import router from '../router';
// import router from '@/router/index';
import { reactive, ref , onMounted, watch } from 'vue'
import axios from 'axios';
import { useSessionStore, loadSessionStore, API_URL } from "@/stores/session.ts"
import SingleQuestion from '@/components/SingleQuestion.vue'
import SingleAnswer from '@/components/SingleAnswer.vue'
import ExamSummary from '@/components/ExamSummary.vue'

const props = defineProps<{
  result_id: string
}>()

const uuidv4 = () => {
    let seed = Date.now()
    const nValues = [8, 9, 'a', 'b']
    // "xxxxxxxx-xxxx-Mxxx-Nxxx-xxxxxxxxxxxx"
    // x is one of [0-9, a-f] M is one of [1-5], and N is [8, 9, a, or b]
    return 'xxxxxxxx-xxxx-4xxx-Nxxx-xxxxxxxxxxxx'.replace(/[xN]/g, c => {
        if (c === 'N') {
            const key = Math.round(Math.random() * 10 % 3)
            return nValues[key].toString(16)
        } else {
            seed = seed / (1 + Math.random())
            return (seed % 16 | 0).toString(16)
        }
    })
}

const session = useSessionStore()
const questions = reactive([])
const answers = reactive([])
const idx = ref(0)
const a_idx = ref(0)
const finished = ref(false)
const display_summary = ref(false)
const passed = ref(true)
const examId = ref(Math.random())
const t_start = ref(0)
const t_end = ref(0)
const exam_result_id = uuidv4()

const gotAnswer = (question, index, answer) => {
    answers.push({ question, index, answer })
    if (idx.value + 1 < questions.length) {
        idx.value++
    } else {
        finished.value = true
        display_summary.value = true
        examId.value = Math.random()
        t_end.value = Date.now()
    }
}

const showAnswer = (idx : any) => {
    if (idx == 'summary') {
        a_idx.value = 0
        display_summary.value = true
    } else {
        a_idx.value = idx
        display_summary.value = false
    }
}



watch(finished, () => {
    axios.post(API_URL + "?action=store_result", {
        result_id: exam_result_id,
        session_id: session.id,
        result: JSON.stringify({
            t_start: t_start.value,
            t_end: t_end.value,
            answers: { ...answers }
        })
    })
})

onMounted(() => {
    loadSessionStore(session)
    
    if (props.result_id) {
        axios.get(API_URL + '?action=get_result&result_id=' + props.result_id).then(res => {
            if (!res.data || !res.data.result) {
                router.push('/')
            } else {
                const result = JSON.parse(res.data.result.result)
                Object.keys(result.answers).forEach(key => answers.push(result.answers[key]))
                display_summary.value = true
                finished.value = true
                t_start.value = result.t_start
                t_end.value = result.t_end
            }
        })
    } else {
        axios.post(API_URL + "?action=update_session", {
            session_id: session.id,
            category: session.category,
            type: 'exam'
        })
            
        axios.get(API_URL + '?action=exam&category=' + session.category).then(res => {
            if (res && res.data) {
                questions.push(...res.data.questions)
            }
        }).then( () => {
            t_start.value = Date.now()
        })
    }
})
defineExpose({ questions, exam_result_id })
</script>

<style>
.question-block {
    width: 350px;
}
.question-media{
    width: 350px;
    height: 196px;
    background-size: contain;
}   

/* @media(max-height: 768px) {
    .question-block {
        width: 665px;
    }
    .question-media{
        width: 665px;
        height: 374px;
    }   
} */
@media(min-width: 640px) {
    .question-block {
        width: 620px;
    }
    .question-media{
        width: 620px;
        height: 348px;
    }   
}

@media(min-width: 768px) {
    .question-block {
        width: 760px;
    }
    .question-media{
        width: 760px;
        height: 425px;
    }   
}

@media(min-width: 1024px) {
    .question-block {
        width: 1024px;
    }
    .question-media{
        width: 1024px;
        height: 576px;
     
    }
}
</style>