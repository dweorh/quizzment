<script setup lang="ts">
import { useSessionStore } from "@/stores/session"
import router from '@/router/index';
const categories = ['A', 'B', 'C', 'D', 'T', 'AM', 'A1', 'A2', 'B1', 'C1', 'D1', 'PT' ];
const session = useSessionStore()
if (session.category !== '') {
  router.push('/lesson')
}
const selectCategory = (category: string) => {
  session.setCategory(category)
  router.push('/lesson')
}
</script>

<template>
  <main class="flex flex-col">
    <div class="lg:w-1/2 pl-1 mb-2 self-center mt-1">
      Strona powstała aby umożliwić każdemu chętnemu sprawdzenie się z teorią ruchu drogowego.
    </div>
    <div class="lg:w-1/2 pl-1 mb-2 self-center mt-1">
      Każdy egzamin składa się z 32 pytań.<br />20 pytań ogólnych i 12 pytań specjalistycznych.<br />Na ich rozwiązanie jest ok. 25 minut.
    </div>
    <div class="lg:w-1/2 pl-1 mb-2 self-center mt-1">
      Każde pytanie ma przypisaną liczbę punktów od 1 do 3 punktów.<br />Żeby zaliczyć egzamin, można 'zgubić' ich maksymalnie 6.<br />
      Do zdobycia są 74 punkty, do zaliczenia potrzeba 68.
    </div>
    <div class="lg:w-1/2 pl-1 self-center">
      Pytania oraz oryginalne materiały do nich pochodzą ze strony Ministerstwa Infrastruktury i są aktualne na rok 2022.
    </div>
    <div class="mb-2 self-center mt-1">Wybierz kategorię by rozpocząć testy.</div>
    <div class="grid grid-cols-3 gap-4 w-fit self-center mb-4">
        <div
        v-for="name in categories"
        :key="name"
        @click="selectCategory(name)"
        class="w-24 border text-center hover:bg-sky-400 cursor-pointer"
        >
            {{name}}
        </div>
    </div>
    <div class="lg:w-1/2 pl-1 mb-2 self-center">
      Dane dotyczące sesji są przechowywane w lokalnym magazynie Twojej
      przeglądarki. Wyczyszczenie jej cache'a spowoduje utratę danych tam
      zapisanych, m.in. informacji o postępach.
    </div>   
  </main>
</template>
