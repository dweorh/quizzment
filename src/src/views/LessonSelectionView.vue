/* eslint-disable prettier/prettier */
<script setup lang="ts">
// import { RouterLink } from 'vue-router'
import { useSessionStore } from "@/stores/session"
import type { Lesson } from '@/types/lesson'
import router from '@/router/index';

const lessons : Array<Lesson> = [
    // { name: "Nauka - wszystkie pytania", type: 'practice' },
    { name: "Egzamin - losowy zestaw pytań", type: 'exam'}
]
// const categories = ['A', 'B', 'C', 'D', 'T', 'AM', 'A1', 'A2', 'B1', 'C1', 'D1', 'PT' ];
// import TheWelcome from '@/components/TheWelcome.vue'
const session = useSessionStore()
const selectLesson = (lesson : Lesson) => {
    session.setLesson(lesson)
    router.push('/' + lesson.type)
}

const changeCategory = () => {
  session.setCategory('')
  router.push('/')
}

</script>

<template>
  <main class="flex flex-col">
    <div class="lg:w-1/2 mb-2 self-center">
       <a @click.prevent="changeCategory" class="cursor-pointer">Zmień kategorię ({{session.category}})</a>
    </div>
    <div class="flex flex-col">
        <div
        v-for="lesson in lessons"
        :key="lesson.name"
        @click="selectLesson(lesson)"
        class="border text-center hover:bg-sky-400 cursor-pointer mb-4 p-4 mx-1"
        >
            {{lesson.name}}
        </div>
    </div>
  </main>
</template>
