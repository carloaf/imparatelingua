<template>
  <div class="question-card bg-white rounded-lg shadow-lg p-8">
    <!-- Contexto da questão -->
    <div v-if="question.context" class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
      <p class="text-base text-gray-800 italic leading-relaxed" v-html="formattedContext"></p>
    </div>

    <!-- Pergunta -->
    <div class="mb-6">
      <div class="flex items-start justify-between mb-4">
        <h3 class="text-xl font-bold text-gray-800 flex-1">{{ question.question_text }}</h3>
        <div class="flex items-center gap-2 ml-4">
          <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600">
            Dificuldade: {{ '⭐'.repeat(question.difficulty) }}
          </span>
          <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-600 capitalize">
            {{ questionTypeLabel }}
          </span>
        </div>
      </div>
    </div>

    <!-- Questão de múltipla escolha -->
    <div v-if="isMultipleChoiceLike" class="space-y-3">
      <div
        v-for="(answer, index) in question.answers_ordered"
        :key="answer.id"
        @click="!answered && selectAnswer(answer)"
        :class="[
          'answer-option p-4 rounded-lg border-2 cursor-pointer transition-all',
          getAnswerClass(answer),
          !answered && 'hover:border-blue-400 hover:bg-blue-50'
        ]"
      >
        <div class="flex items-center">
          <span class="font-bold mr-3 text-lg">{{ String.fromCharCode(65 + index) }}.</span>
          <span class="flex-1">{{ answer.answer_text }}</span>
          <span v-if="answered && answer.is_correct" class="text-green-600 text-xl">✓</span>
          <span v-if="answered && selectedAnswer?.id === answer.id && !answer.is_correct" class="text-red-600 text-xl">✗</span>
        </div>
      </div>
    </div>

    <!-- Questão de preencher lacuna -->
    <div v-else-if="question.question_type === 'fill_in_blank'" class="space-y-4">
      <div class="flex items-center gap-4">
        <input
          ref="fillBlankInputRef"
          v-model="textAnswer"
          @keyup.enter="submitTextAnswer"
          :disabled="answered"
          type="text"
          placeholder="Digite sua resposta aqui..."
          class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none disabled:bg-gray-100 disabled:cursor-not-allowed text-lg"
        />
        <button
          v-if="!answered"
          @click="submitTextAnswer"
          :disabled="!textAnswer.trim()"
          class="px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
        >
          Confirmar
        </button>
      </div>
      <p class="text-sm text-gray-600">
        💡 Dica: Digite sua resposta e pressione Enter ou clique em "Confirmar"
      </p>
    </div>

    <!-- Questão verdadeiro/falso -->
    <div v-else-if="question.question_type === 'true_false'" class="space-y-3">
      <div
        v-for="option in trueFalseOptions"
        :key="option.value"
        @click="!answered && selectTrueFalse(option)"
        :class="[
          'answer-option p-4 rounded-lg border-2 cursor-pointer transition-all',
          getTrueFalseClass(option),
          !answered && 'hover:border-blue-400 hover:bg-blue-50'
        ]"
      >
        <div class="flex items-center justify-between">
          <span class="text-lg font-semibold">{{ option.label }}</span>
          <span v-if="answered && option.value === correctAnswer?.answer_text" class="text-green-600 text-xl">✓</span>
          <span v-if="answered && selectedTrueFalse === option.value && option.value !== correctAnswer?.answer_text" class="text-red-600 text-xl">✗</span>
        </div>
      </div>
    </div>

    <!-- Questão de seleção múltipla (múltiplas respostas corretas) -->
    <div v-else-if="question.question_type === 'multiple_selection'" class="space-y-3">
      <p class="text-sm text-blue-600 mb-4">⚠️ Esta questão pode ter mais de uma resposta correta</p>
      <div
        v-for="(answer, index) in question.answers_ordered"
        :key="answer.id"
        @click="!answered && toggleSelection(answer)"
        :class="[
          'answer-option p-4 rounded-lg border-2 cursor-pointer transition-all',
          getMultipleSelectionClass(answer),
          !answered && 'hover:border-blue-400 hover:bg-blue-50'
        ]"
      >
        <div class="flex items-center">
          <div class="mr-3">
            <input 
              type="checkbox" 
              :checked="isSelected(answer)"
              :disabled="answered"
              class="w-5 h-5 text-blue-600 rounded pointer-events-none"
            />
          </div>
          <span class="font-bold mr-3 text-lg">{{ String.fromCharCode(65 + index) }}.</span>
          <span class="flex-1">{{ answer.answer_text }}</span>
          <span v-if="answered && answer.is_correct" class="text-green-600 text-xl">✓</span>
          <span v-if="answered && isSelected(answer) && !answer.is_correct" class="text-red-600 text-xl">✗</span>
        </div>
      </div>
      <button
        v-if="!answered && selectedAnswers.length > 0"
        @click="submitMultipleSelection"
        class="w-full mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors"
      >
        Confirmar Seleção ({{ selectedAnswers.length }} selecionadas)
      </button>
    </div>

    <!-- Questão de preencher múltiplas lacunas -->
    <div v-else-if="question.question_type === 'fill_in_the_blanks'" class="space-y-4">
      <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
        <p class="text-sm text-gray-700">
          📝 Preencha todas as lacunas numeradas. Cada resposta deve ser escrita no campo correspondente.
        </p>
      </div>
      <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
        <div
          v-for="(answer, index) in question.answers_ordered"
          :key="answer.id"
          class="flex items-center gap-3"
        >
          <span class="font-bold text-gray-600 min-w-[40px]">{{ index + 1 }}.</span>
          <input
            :ref="el => setFillBlanksInputRef(el, index)"
            v-model="fillBlanksAnswers[index]"
            :disabled="answered"
            type="text"
            :placeholder="`Resposta ${index + 1}`"
            :class="[
              'flex-1 px-3 py-2 border-2 rounded-lg focus:outline-none text-base',
              answered && answer.is_correct && fillBlanksAnswers[index]?.toLowerCase().trim() === answer.answer_text.toLowerCase().trim()
                ? 'border-green-500 bg-green-50'
                : answered && fillBlanksAnswers[index]?.trim()
                ? 'border-red-500 bg-red-50'
                : 'border-gray-300 focus:border-blue-500'
            ]"
          />
          <span v-if="answered && answer.is_correct && fillBlanksAnswers[index]?.toLowerCase().trim() === answer.answer_text.toLowerCase().trim()" class="text-green-600 text-xl">✓</span>
          <span v-else-if="answered && fillBlanksAnswers[index]?.trim()" class="text-red-600 text-xl">✗</span>
        </div>
      </div>
      <button
        v-if="!answered"
        @click="submitFillBlanks"
        :disabled="fillBlanksAnswers.filter(a => a?.trim()).length === 0"
        class="w-full mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
      >
        Confirmar Respostas
      </button>
    </div>

    <!-- Questão de múltipla escolha com lacunas (Cloze test) -->
    <div v-else-if="question.question_type === 'multiple_choice_cloze'" class="space-y-4">
      <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-4">
        <p class="text-sm text-gray-700">
          🎯 Para cada lacuna numerada, escolha a opção correta entre as alternativas apresentadas.
        </p>
      </div>

      <div v-if="hasStructuredCloze" class="space-y-3 max-h-96 overflow-y-auto pr-2">
        <div
          v-for="(answer, index) in question.answers_ordered"
          :key="answer.id"
          class="border rounded-lg p-3 bg-white"
        >
          <div class="flex items-start gap-3">
            <span class="font-bold text-gray-600 min-w-[40px] mt-2">{{ index + 1 }}.</span>
            <div class="flex-1">
              <p v-if="answer.question_text" class="text-sm text-gray-600 mb-2">{{ answer.question_text }}</p>
              <select
                v-model="clozeAnswers[index]"
                :disabled="answered"
                :class="[
                  'w-full px-3 py-2 border-2 rounded-lg focus:outline-none',
                  answered && clozeAnswers[index] === answer.answer_text
                    ? 'border-green-500 bg-green-50'
                    : answered && clozeAnswers[index]
                    ? 'border-red-500 bg-red-50'
                    : 'border-gray-300 focus:border-blue-500'
                ]"
              >
                <option value="">-- Selecione --</option>
                <option v-for="opt in parseOptions(answer.options)" :key="opt" :value="opt">
                  {{ opt }}
                </option>
              </select>
            </div>
            <span v-if="answered && clozeAnswers[index] === answer.answer_text" class="text-green-600 text-xl mt-2">✓</span>
            <span v-else-if="answered && clozeAnswers[index]" class="text-red-600 text-xl mt-2">✗</span>
          </div>
        </div>
      </div>

      <div v-else class="space-y-3">
        <p class="text-gray-800 font-semibold">{{ question.question_text }}</p>
        <select
          v-model="clozeSingleAnswer"
          :disabled="answered"
          :class="[
            'w-full px-3 py-2 border-2 rounded-lg focus:outline-none text-base',
            answered && clozeSingleAnswer === correctAnswer?.answer_text
              ? 'border-green-500 bg-green-50'
              : answered && clozeSingleAnswer
              ? 'border-red-500 bg-red-50'
              : 'border-gray-300 focus:border-blue-500'
          ]"
        >
          <option value="">-- Selecione --</option>
          <option
            v-for="answer in question.answers_ordered"
            :key="answer.id"
            :value="answer.answer_text"
          >
            {{ answer.answer_text }}
          </option>
        </select>
        <div v-if="answered" class="text-sm text-gray-700">
          <span v-if="clozeSingleAnswer === correctAnswer?.answer_text" class="text-green-600 font-semibold">✓ Correto</span>
          <span v-else class="text-red-600 font-semibold">✗ Incorreto</span>
        </div>
      </div>

      <button
        v-if="!answered"
        @click="submitCloze"
        :disabled="!canSubmitCloze"
        class="w-full mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
      >
        Confirmar Respostas
      </button>
    </div>

    <!-- Questão de ordenação -->
    <div v-else-if="question.question_type === 'ordering'" class="space-y-4">
      <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-4">
        <p class="text-sm text-gray-700">
          🔢 Digite a ordem correta (1, 2, 3, ...) para cada trecho e reconstrua o texto.
        </p>
      </div>
      <div class="space-y-3">
        <div
          v-for="(answer, index) in orderingItems"
          :key="answer.id || index"
          class="flex items-start gap-3 border rounded-lg p-3 bg-white"
        >
          <input
            :ref="el => setOrderingInputRef(el, index)"
            v-model.number="orderingInputs[index]"
            type="number"
            min="1"
            :max="orderingItems.length"
            :disabled="answered"
            :class="[
              'w-16 px-3 py-2 border-2 rounded-lg text-center font-semibold',
              answered && Number(orderingInputs[index]) === answer.order
                ? 'border-green-500 bg-green-50 text-green-700'
                : answered && orderingInputs[index]
                ? 'border-red-500 bg-red-50 text-red-700'
                : 'border-gray-300 focus:border-blue-500'
            ]"
          />
          <div class="flex-1">
            <p class="text-gray-800 leading-relaxed">{{ answer.answer_text }}</p>
            <p v-if="answered" class="text-xs text-gray-500 mt-1">Posição correta: {{ answer.order }}</p>
          </div>
        </div>
      </div>
      <button
        v-if="!answered"
        @click="submitOrdering"
        :disabled="!orderingReady"
        class="w-full mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
      >
        Confirmar Ordem
      </button>
    </div>

    <!-- Questão de associação/matching -->
    <div v-else-if="question.question_type === 'matching' && !isMatchingAsChoices" class="space-y-4">
      <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-4">
        <p class="text-sm text-gray-700">
          🔗 Associe cada item da esquerda com a situação correspondente da direita.
        </p>
      </div>
      <div class="space-y-3">
        <div
          v-for="(answer, index) in question.answers_ordered"
          :key="answer.id"
          class="border rounded-lg p-4 bg-white"
        >
          <div class="mb-3">
            <span class="font-bold text-gray-700">{{ index + 1 }}.</span>
            <p class="text-gray-800 mt-1 pl-6">{{ answer.question_text || answer.answer_text }}</p>
          </div>
          <div class="pl-6">
            <p class="text-xs text-gray-600 mb-2">Escolha a situação correspondente:</p>
            <select
              v-model="matchingAnswers[index]"
              :disabled="answered"
              :class="[
                'w-full px-3 py-2 border-2 rounded-lg focus:outline-none text-sm',
                answered && matchingAnswers[index] === answer.is_correct_option
                  ? 'border-green-500 bg-green-50'
                  : answered && matchingAnswers[index]
                  ? 'border-red-500 bg-red-50'
                  : 'border-gray-300 focus:border-blue-500'
              ]"
            >
              <option value="">-- Selecione a situação --</option>
              <option v-for="opt in parseMatchingOptions(answer.options)" :key="opt.value" :value="opt.value">
                {{ opt.label }}
              </option>
            </select>
            <span v-if="answered && matchingAnswers[index] === answer.is_correct_option" class="text-green-600 text-sm ml-2">✓ Correto</span>
            <span v-else-if="answered && matchingAnswers[index]" class="text-red-600 text-sm ml-2">
              ✗ Incorreto (Resposta correta: {{ answer.is_correct_option }})
            </span>
          </div>
        </div>
      </div>
      <button
        v-if="!answered"
        @click="submitMatching"
        :disabled="matchingAnswers.filter(a => a).length === 0"
        class="w-full mt-4 px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
      >
        Confirmar Associações
      </button>
    </div>

    <!-- Feedback -->
    <div v-if="answered" class="mt-6 space-y-4">
      <!-- Resultado -->
      <div :class="[
        'p-4 rounded-lg',
        isCorrect ? 'bg-green-100 border-2 border-green-500' : 'bg-red-100 border-2 border-red-500'
      ]">
        <p :class="[
          'font-bold mb-2',
          isCorrect ? 'text-green-800' : 'text-red-800'
        ]">
          {{ isCorrect ? '🎉 Parabéns! Resposta correta!' : '❌ Resposta incorreta' }}
        </p>
        <p v-if="!isCorrect" class="text-sm text-gray-700">
          A resposta correta é: <strong>{{ correctAnswerText }}</strong>
        </p>
      </div>

      <!-- Justificativa da resposta selecionada -->
      <div v-if="currentJustification" class="bg-blue-50 border-l-4 border-blue-500 p-4">
        <p class="text-sm font-semibold text-blue-800 mb-1">💡 Justificativa:</p>
        <p class="text-sm text-gray-700">{{ currentJustification }}</p>
      </div>

      <!-- Explicação do conceito -->
      <div v-if="question.explanation" class="bg-purple-50 border-l-4 border-purple-500 p-4">
        <p class="text-sm font-semibold text-purple-800 mb-1">📚 Explicação do conceito:</p>
        <p class="text-sm text-gray-700">{{ question.explanation }}</p>
      </div>

      <!-- Justificativas de todas as alternativas (apenas para múltipla escolha) -->
      <div v-if="question.question_type === 'multiple_choice' && hasJustifications" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <p class="text-sm font-semibold text-gray-800 mb-3">📝 Análise de todas as alternativas:</p>
        <div class="space-y-2">
          <div 
            v-for="(answer, index) in question.answers_ordered" 
            :key="answer.id"
            class="text-sm"
          >
            <div v-if="answer.justification" class="flex items-start gap-2">
              <span class="font-bold text-gray-600 min-w-[24px]">{{ String.fromCharCode(65 + index) }}.</span>
              <div class="flex-1">
                <span :class="answer.is_correct ? 'text-green-700 font-semibold' : 'text-gray-700'">
                  {{ answer.answer_text }}
                </span>
                <span v-if="answer.is_correct" class="text-green-600 ml-1">✓</span>
                <p class="text-gray-600 mt-1">{{ answer.justification }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, nextTick, onBeforeUpdate, onBeforeUnmount } from 'vue'

export default {
  name: 'QuestionCard',
  props: {
    question: {
      type: Object,
      required: true
    },
    savedAnswer: {
      type: Object,
      default: null
    },
    questionIndex: {
      type: Number,
      default: 0
    }
  },
  emits: ['answer'],
  setup(props, { emit }) {
    const selectedAnswer = ref(null)
    const answered = ref(false)
    const isCorrect = ref(false)
    const textAnswer = ref('')
    const selectedTrueFalse = ref(null)
    const selectedAnswers = ref([])
    const fillBlanksAnswers = ref([])
    const clozeAnswers = ref([])
    const clozeSingleAnswer = ref('')
    const matchingAnswers = ref([])
    const orderingInputs = ref([])
    const orderingItems = ref([])
    const fillBlankInputRef = ref(null)
    const fillBlanksInputRefs = ref([])
    const orderingFieldRefs = ref([])
    const isMounted = ref(true)

    onBeforeUpdate(() => {
      if (isMounted.value) {
        fillBlanksInputRefs.value = []
        orderingFieldRefs.value = []
      }
    })

    onBeforeUnmount(() => {
      isMounted.value = false
      fillBlanksInputRefs.value = []
      orderingFieldRefs.value = []
      fillBlankInputRef.value = null
    })

    const trueFalseOptions = [
      { value: 'true', label: '✓ Verdadeiro' },
      { value: 'false', label: '✗ Falso' }
    ]

    const questionTypeLabel = computed(() => {
      const types = {
        'multiple_choice': 'Múltipla Escolha',
        'fill_in_blank': 'Preencher Lacuna',
        'true_false': 'Verdadeiro/Falso',
        'multiple_selection': 'Seleção Múltipla',
        'fill_in_the_blanks': 'Preencher Lacunas',
        'multiple_choice_cloze': 'Cloze Test',
        'matching': 'Associação',
        'ordering': 'Ordenação',
        'reorder_text': 'Reordenar Texto'
      }
      return types[props.question.question_type] || props.question.question_type
    })

    const matchingHasDropdownOptions = computed(() => {
      return (props.question.answers_ordered || []).some(answer => answer?.options || answer?.is_correct_option)
    })

    const isMatchingAsChoices = computed(() => {
      return props.question.question_type === 'matching' && !matchingHasDropdownOptions.value
    })

    const isMultipleChoiceLike = computed(() => {
      return props.question.question_type === 'multiple_choice' || isMatchingAsChoices.value
    })

    const highlightableContextTypes = ['fill_in_blank', 'fill_in_the_blanks', 'multiple_choice_cloze']

    const shouldHighlightContext = computed(() => {
      return highlightableContextTypes.includes(props.question.question_type)
    })

    const highlightAllBlanks = computed(() => props.question.question_type === 'fill_in_the_blanks')

    const setFillBlanksInputRef = (el, index) => {
      if (el && isMounted.value) {
        fillBlanksInputRefs.value[index] = el
      }
    }

    const setOrderingInputRef = (el, index) => {
      if (el && isMounted.value) {
        orderingFieldRefs.value[index] = el
      }
    }

    const escapeHtml = (value = '') => {
      return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
    }

    const lineHasBlank = (line = '') => {
      return /__\(?\d+\)?__|_{3,}/.test(line)
    }

    const extractKeywordsFromQuestion = () => {
      const text = props.question.question_text || ''
      const stopWords = ['o', 'a', 'os', 'as', 'um', 'uma', 'de', 'do', 'da', 'dos', 'das', 'em', 'no', 'na', 'nos', 'nas', 'e', 'é', 'para', 'com', 'por', 'que', 'se', 'ao', 'aos', 'à', 'às', 'il', 'lo', 'la', 'i', 'gli', 'le', 'di', 'da', 'in', 'con', 'su', 'per', 'tra', 'fra', 'a', 'e', 'ma', 'o', 'se', 'che', 'the', 'is', 'are', 'of', 'to', 'and', 'or']
      
      const cleaned = text
        .replace(/^[0-9]{1,2}[\.\)\-:]\s*/, '')
        .replace(/\([0-9]{1,2}\)/g, '')
        .replace(/_+/g, ' ')
        .replace(/[.,;:!?\(\)\[\]\{\}"']/g, ' ')
        .toLowerCase()
      
      const words = cleaned.split(/\s+/).filter(w => {
        return w.length >= 3 && !stopWords.includes(w) && !/^\d+$/.test(w)
      })
      
      return words.slice(0, 5)
    }

    const findKeywordInContext = (contextText = '', keywords = []) => {
      if (!keywords.length) return -1
      
      const lowerContext = contextText.toLowerCase()
      for (const keyword of keywords) {
        const idx = lowerContext.indexOf(keyword)
        if (idx !== -1) {
          return idx
        }
      }
      return -1
    }

    const formattedContext = computed(() => {
      if (!props.question.context) return ''
      
      if (!shouldHighlightContext.value) {
        return escapeHtml(props.question.context).replace(/\n/g, '<br>')
      }

      if (highlightAllBlanks.value) {
        const lines = props.question.context.split('\n')
        return lines.map(rawLine => {
          const trimmed = rawLine.trim()
          const safeLine = escapeHtml(trimmed)
          if (!safeLine) return ''
          const highlight = lineHasBlank(rawLine)
          if (highlight) {
            return `<span class="px-1 rounded bg-yellow-100 text-yellow-900 font-semibold">${safeLine}</span>`
          }
          return safeLine
        }).join('<br>')
      }

      const keywords = extractKeywordsFromQuestion()
      if (!keywords.length) {
        return escapeHtml(props.question.context).replace(/\n/g, '<br>')
      }

      const contextText = props.question.context
      
      // Extrair número da questão do texto (ex: "1. La seconda..." -> 1)
      const questionNumberMatch = props.question.question_text.match(/^(\d+)\./)
      const questionNumber = questionNumberMatch ? parseInt(questionNumberMatch[1]) : null
      
      const blankPattern = /__\(\d+\)__|_{3,}/g
      const allBlanks = []
      let match
      
      while ((match = blankPattern.exec(contextText)) !== null) {
        allBlanks.push({
          index: match.index,
          length: match[0].length,
          text: match[0]
        })
      }
      
      if (!allBlanks.length) {
        return escapeHtml(contextText).replace(/\n/g, '<br>')
      }

      // Se temos lacunas numeradas __(1)__, buscar pela lacuna com o número correto
      // Senão, usar o índice sequencial
      let currentBlank
      if (allBlanks[0].text.includes('(') && questionNumber) {
        // Lacunas numeradas: buscar pela lacuna __(n)__ que corresponde ao número da questão
        const targetPattern = `__(${questionNumber})__`
        currentBlank = allBlanks.find(b => b.text === targetPattern)
      } else {
        // Lacunas não numeradas: usar o índice sequencial
        const blankIndex = questionNumber ? questionNumber - 1 : props.questionIndex
        currentBlank = allBlanks[blankIndex]
      }
      if (!currentBlank) {
        return escapeHtml(contextText).replace(/\n/g, '<br>')
      }
      
      const blankStart = currentBlank.index
      const blankEnd = blankStart + currentBlank.length
      
      const beforeChars = 30
      const afterChars = 30
      
      let startPos = Math.max(0, blankStart - beforeChars)
      while (startPos > 0 && /\S/.test(contextText[startPos - 1]) && contextText[startPos] !== ' ') {
        startPos--
      }
      if (startPos > 0 && contextText[startPos] === ' ') startPos++
      
      let endPos = Math.min(contextText.length, blankEnd + afterChars)
      while (endPos < contextText.length && /\S/.test(contextText[endPos]) && contextText[endPos] !== ' ') {
        endPos++
      }

      const beforeText = contextText.substring(0, startPos)
      const highlightText = contextText.substring(startPos, endPos)
      const afterText = contextText.substring(endPos)
      
      const safeBefore = escapeHtml(beforeText).replace(/\n/g, '<br>')
      const safeHighlight = escapeHtml(highlightText).replace(/\n/g, '<br>')
      const safeAfter = escapeHtml(afterText).replace(/\n/g, '<br>')
      
      return `${safeBefore}<span class="px-1 rounded bg-yellow-100 text-yellow-900 font-semibold">${safeHighlight}</span>${safeAfter}`
    })

    const hasStructuredCloze = computed(() => {
      return (props.question.answers_ordered || []).some(answer => !!answer?.options)
    })

    const canSubmitCloze = computed(() => {
      if (hasStructuredCloze.value) {
        const blanks = props.question.answers_ordered?.length || 0
        return blanks > 0 && clozeAnswers.value.filter(a => a).length === blanks
      }
      return Boolean(clozeSingleAnswer.value)
    })

    const orderingReady = computed(() => {
      if (!orderingItems.value.length) return false
      return orderingInputs.value.length === orderingItems.value.length &&
        orderingInputs.value.every(value => value !== null && value !== '' && !Number.isNaN(Number(value)))
    })

    const correctAnswer = computed(() => {
      return props.question.answers_ordered?.find(a => a.is_correct) || 
             props.question.answers?.find(a => a.is_correct)
    })

    const getAnswerById = (answerId) => {
      if (!answerId) return null
      return (props.question.answers_ordered || props.question.answers || []).find(answer => answer.id === answerId) || null
    }

    const correctAnswerText = computed(() => {
      return correctAnswer.value?.answer_text || ''
    })

    // Justificativa da resposta selecionada
    const currentJustification = computed(() => {
      if (isMultipleChoiceLike.value) {
        return selectedAnswer.value?.justification || null
      } else if (props.question.question_type === 'fill_in_blank') {
        return correctAnswer.value?.justification || null
      } else if (props.question.question_type === 'true_false') {
        return correctAnswer.value?.justification || null
      }
      return null
    })

    // Verifica se há justificativas nas alternativas
    const hasJustifications = computed(() => {
      return (props.question.answers_ordered || []).some(a => a.justification)
    })

    // Múltipla escolha
    const selectAnswer = (answer) => {
      if (answered.value) return

      selectedAnswer.value = answer
      answered.value = true
      isCorrect.value = answer.is_correct

      emit('answer', {
        questionId: props.question.id,
        answerId: answer.id,
        isCorrect: answer.is_correct,
        userAnswer: answer.answer_text,
        questionType: props.question.question_type,
        selection: {
          selectedAnswerId: answer.id
        }
      })
    }

    const getAnswerClass = (answer) => {
      if (!answered.value) {
        return 'border-gray-300 bg-white'
      }

      if (answer.is_correct) {
        return 'border-green-500 bg-green-50'
      }

      if (selectedAnswer.value?.id === answer.id && !answer.is_correct) {
        return 'border-red-500 bg-red-50'
      }

      return 'border-gray-200 bg-gray-50 opacity-60'
    }

    // Preencher lacuna
    const submitTextAnswer = () => {
      if (!textAnswer.value.trim() || answered.value) return

      answered.value = true
      
      // Verificar contra todas as respostas corretas
      const correctAnswers = (props.question.answers_ordered || props.question.answers || [])
        .filter(a => a.is_correct)
        .map(a => a.answer_text.trim().toLowerCase())
      
      const userAnswerLower = textAnswer.value.trim().toLowerCase()
      const correct = correctAnswers.includes(userAnswerLower)
      isCorrect.value = correct

      emit('answer', {
        questionId: props.question.id,
        answerId: correctAnswer.value?.id,
        isCorrect: correct,
        userAnswer: textAnswer.value.trim(),
        questionType: props.question.question_type,
        selection: {
          textAnswer: textAnswer.value.trim()
        }
      })
    }

    // Verdadeiro/Falso
    const selectTrueFalse = (option) => {
      if (answered.value) return

      selectedTrueFalse.value = option.value
      answered.value = true
      const correct = option.value === correctAnswerText.value
      isCorrect.value = correct

      emit('answer', {
        questionId: props.question.id,
        answerId: correctAnswer.value?.id,
        isCorrect: correct,
        userAnswer: option.label,
        questionType: props.question.question_type,
        selection: {
          trueFalseValue: option.value
        }
      })
    }

    const getTrueFalseClass = (option) => {
      if (!answered.value) {
        return 'border-gray-300 bg-white'
      }

      if (option.value === correctAnswerText.value) {
        return 'border-green-500 bg-green-50'
      }

      if (selectedTrueFalse.value === option.value && option.value !== correctAnswerText.value) {
        return 'border-red-500 bg-red-50'
      }

      return 'border-gray-200 bg-gray-50 opacity-60'
    }

    // Seleção múltipla
    const toggleSelection = (answer) => {
      const index = selectedAnswers.value.findIndex(a => a.id === answer.id)
      if (index > -1) {
        selectedAnswers.value.splice(index, 1)
      } else {
        selectedAnswers.value.push(answer)
      }
    }

    const isSelected = (answer) => {
      return selectedAnswers.value.some(a => a.id === answer.id)
    }

    const submitMultipleSelection = () => {
      if (answered.value) return

      answered.value = true
      const correctAnswers = props.question.answers_ordered?.filter(a => a.is_correct) || []
      const selectedIds = selectedAnswers.value.map(a => a.id).sort()
      const correctIds = correctAnswers.map(a => a.id).sort()
      
      isCorrect.value = JSON.stringify(selectedIds) === JSON.stringify(correctIds)

      emit('answer', {
        questionId: props.question.id,
        answerId: selectedAnswers.value.map(a => a.id),
        isCorrect: isCorrect.value,
        userAnswer: selectedAnswers.value.map(a => a.answer_text).join(', '),
        questionType: props.question.question_type,
        selection: {
          selectedAnswerIds: selectedAnswers.value.map(a => a.id)
        }
      })
    }

    const getMultipleSelectionClass = (answer) => {
      if (!answered.value) {
        return isSelected(answer) ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-white'
      }

      if (answer.is_correct) {
        return 'border-green-500 bg-green-50'
      }

      if (isSelected(answer) && !answer.is_correct) {
        return 'border-red-500 bg-red-50'
      }

      return 'border-gray-200 bg-gray-50 opacity-60'
    }

    // Preencher múltiplas lacunas
    const submitFillBlanks = () => {
      if (answered.value) return

      answered.value = true
      const correctAnswers = props.question.answers_ordered?.filter(a => a.is_correct) || []
      let correctCount = 0

      correctAnswers.forEach((answer, index) => {
        const userAnswer = fillBlanksAnswers.value[index]?.toLowerCase().trim() || ''
        const correctAnswer = answer.answer_text.toLowerCase().trim()
        if (userAnswer === correctAnswer) {
          correctCount++
        }
      })

      isCorrect.value = correctCount === correctAnswers.length

      emit('answer', {
        questionId: props.question.id,
        answerId: null,
        isCorrect: isCorrect.value,
        userAnswer: fillBlanksAnswers.value.join(' | '),
        correctCount: correctCount,
        totalCount: correctAnswers.length,
        questionType: props.question.question_type,
        selection: {
          fillBlanksAnswers: [...fillBlanksAnswers.value]
        }
      })
    }

    // Cloze test (múltipla escolha em lacunas)
    const submitCloze = () => {
      if (answered.value || !canSubmitCloze.value) return

      if (!hasStructuredCloze.value) {
        clozeAnswers.value = [clozeSingleAnswer.value]
      }

      answered.value = true
      const correctAnswers = props.question.answers_ordered?.filter(a => a.is_correct) || []
      let correctCount = 0

      correctAnswers.forEach((answer, index) => {
        if (clozeAnswers.value[index] === answer.answer_text) {
          correctCount++
        }
      })

      isCorrect.value = correctCount === correctAnswers.length

      emit('answer', {
        questionId: props.question.id,
        answerId: null,
        isCorrect: isCorrect.value,
        userAnswer: clozeAnswers.value.join(' | '),
        correctCount: correctCount,
        totalCount: correctAnswers.length,
        questionType: props.question.question_type,
        selection: {
          clozeAnswers: [...clozeAnswers.value],
          clozeSingleAnswer: clozeSingleAnswer.value
        }
      })
    }

    // Ordenação
    const submitOrdering = () => {
      if (answered.value) return

      const filledValues = orderingInputs.value.filter(value => value !== null && value !== '' && !Number.isNaN(Number(value)))
      if (!filledValues.length) return

      answered.value = true
      let correctCount = 0

      orderingItems.value.forEach((answer, index) => {
        if (Number(orderingInputs.value[index]) === Number(answer.order)) {
          correctCount++
        }
      })

      isCorrect.value = correctCount === orderingItems.value.length

      emit('answer', {
        questionId: props.question.id,
        answerId: null,
        isCorrect: isCorrect.value,
        userAnswer: orderingInputs.value.join(' | '),
        correctCount,
        totalCount: orderingItems.value.length,
        questionType: props.question.question_type,
        selection: {
          orderingInputs: [...orderingInputs.value],
          orderingAnswerIds: orderingItems.value.map(item => item?.id)
        }
      })
    }

    // Associação/Matching
    const submitMatching = () => {
      if (answered.value) return

      answered.value = true
      const correctAnswers = props.question.answers_ordered?.filter(a => a.is_correct) || []
      let correctCount = 0

      correctAnswers.forEach((answer, index) => {
        if (matchingAnswers.value[index] === answer.is_correct_option) {
          correctCount++
        }
      })

      isCorrect.value = correctCount === correctAnswers.length

      emit('answer', {
        questionId: props.question.id,
        answerId: null,
        isCorrect: isCorrect.value,
        userAnswer: matchingAnswers.value.join(' | '),
        correctCount: correctCount,
        totalCount: correctAnswers.length,
        questionType: props.question.question_type,
        selection: {
          matchingAnswers: [...matchingAnswers.value]
        }
      })
    }

    const shuffleArray = (array = []) => {
      const copy = [...array]
      for (let i = copy.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1))
        ;[copy[i], copy[j]] = [copy[j], copy[i]]
      }
      return copy
    }

    // Helper para parsear opções de cloze
    const parseOptions = (options) => {
      if (!options) return []
      if (typeof options === 'string') {
        try {
          return JSON.parse(options)
        } catch {
          return options.split(',').map(o => o.trim())
        }
      }
      return options
    }

    // Helper para parsear opções de matching
    const parseMatchingOptions = (options) => {
      if (!options) return []
      if (typeof options === 'string') {
        try {
          const parsed = JSON.parse(options)
          if (Array.isArray(parsed)) {
            return parsed.map((opt, idx) => ({
              value: String.fromCharCode(65 + idx),
              label: `${String.fromCharCode(65 + idx)}) ${opt}`
            }))
          }
          return []
        } catch {
          return options.split('|').map((opt, idx) => ({
            value: String.fromCharCode(65 + idx),
            label: `${String.fromCharCode(65 + idx)}) ${opt.trim()}`
          }))
        }
      }
      return []
    }

    const focusInputForQuestion = () => {
      if (answered.value || !isMounted.value) return

      nextTick(() => {
        if (!isMounted.value) return

        if (props.question.question_type === 'fill_in_blank' && fillBlankInputRef.value) {
          fillBlankInputRef.value.focus()
          fillBlankInputRef.value.select?.()
          return
        }

        if (props.question.question_type === 'fill_in_the_blanks') {
          const firstInput = fillBlanksInputRefs.value.find(el => el)
          if (firstInput) {
            firstInput.focus()
            firstInput.select?.()
            return
          }
        }

        if (props.question.question_type === 'ordering') {
          const orderingInput = orderingFieldRefs.value.find(el => el)
          if (orderingInput) {
            orderingInput.focus()
            orderingInput.select?.()
          }
        }
      })
    }

    const initializeState = () => {
      const orderedAnswers = props.question.answers_ordered || []

      selectedAnswer.value = null
      answered.value = false
      isCorrect.value = false
      textAnswer.value = ''
      selectedTrueFalse.value = null
      selectedAnswers.value = []
      fillBlanksAnswers.value = props.question.question_type === 'fill_in_the_blanks'
        ? orderedAnswers.map(() => '')
        : []
      clozeAnswers.value = props.question.question_type === 'multiple_choice_cloze' && hasStructuredCloze.value
        ? orderedAnswers.map(() => '')
        : []
      clozeSingleAnswer.value = ''
      matchingAnswers.value = props.question.question_type === 'matching' && !isMatchingAsChoices.value
        ? orderedAnswers.map(() => '')
        : []
      orderingInputs.value = []

      if (props.question.question_type === 'ordering') {
        orderingItems.value = shuffleArray(orderedAnswers)
        orderingInputs.value = orderingItems.value.map(() => '')
      } else {
        orderingItems.value = orderedAnswers
      }
    }

    const reset = () => {
      initializeState()
      focusInputForQuestion()
    }

    const applySavedAnswer = (savedAnswer) => {
      if (!savedAnswer || savedAnswer.questionId !== props.question.id) {
        return
      }

      answered.value = true
      isCorrect.value = savedAnswer.isCorrect ?? false
      const selection = savedAnswer.selection || {}

      switch (props.question.question_type) {
        case 'multiple_choice':
          if (selection.selectedAnswerId || savedAnswer.answerId) {
            selectedAnswer.value = getAnswerById(selection.selectedAnswerId || savedAnswer.answerId)
          }
          break
        case 'fill_in_blank':
          textAnswer.value = selection.textAnswer || savedAnswer.userAnswer || ''
          break
        case 'true_false':
          selectedTrueFalse.value = selection.trueFalseValue || null
          break
        case 'multiple_selection': {
          const ids = selection.selectedAnswerIds || []
          selectedAnswers.value = (props.question.answers_ordered || []).filter(ans => ids.includes(ans.id))
          break
        }
        case 'fill_in_the_blanks':
          fillBlanksAnswers.value = selection.fillBlanksAnswers ? [...selection.fillBlanksAnswers] : fillBlanksAnswers.value
          break
        case 'multiple_choice_cloze':
          if (hasStructuredCloze.value) {
            clozeAnswers.value = selection.clozeAnswers ? [...selection.clozeAnswers] : clozeAnswers.value
          } else {
            clozeSingleAnswer.value = selection.clozeSingleAnswer || savedAnswer.userAnswer || ''
            clozeAnswers.value = selection.clozeAnswers ? [...selection.clozeAnswers] : clozeAnswers.value
          }
          break
        case 'ordering':
          if (selection.orderingAnswerIds?.length) {
            const answers = props.question.answers_ordered || []
            orderingItems.value = selection.orderingAnswerIds
              .map(id => answers.find(ans => ans.id === id))
              .filter(Boolean)
          }
          orderingInputs.value = selection.orderingInputs ? [...selection.orderingInputs] : orderingInputs.value
          break
        case 'matching':
          if (isMatchingAsChoices.value) {
            if (selection.selectedAnswerId || savedAnswer.answerId) {
              selectedAnswer.value = getAnswerById(selection.selectedAnswerId || savedAnswer.answerId)
            }
          } else {
            matchingAnswers.value = selection.matchingAnswers ? [...selection.matchingAnswers] : matchingAnswers.value
          }
          break
      }
    }

    watch(() => props.question.id, () => {
      initializeState()
      if (props.savedAnswer) {
        applySavedAnswer(props.savedAnswer)
      }
      focusInputForQuestion()
    }, { immediate: true })

    watch(() => props.savedAnswer, (newValue) => {
      if (!newValue) return
      if (newValue.questionId !== props.question.id) return
      applySavedAnswer(newValue)
      focusInputForQuestion()
    }, { deep: true })

    return {
      selectedAnswer,
      answered,
      isCorrect,
      textAnswer,
      selectedTrueFalse,
      selectedAnswers,
      fillBlanksAnswers,
      clozeAnswers,
      clozeSingleAnswer,
      matchingAnswers,
      orderingInputs,
      orderingItems,
      trueFalseOptions,
      questionTypeLabel,
      isMultipleChoiceLike,
      isMatchingAsChoices,
      hasStructuredCloze,
      canSubmitCloze,
      orderingReady,
      correctAnswer,
      correctAnswerText,
      currentJustification,
      hasJustifications,
      formattedContext,
      selectAnswer,
      getAnswerClass,
      submitTextAnswer,
      selectTrueFalse,
      getTrueFalseClass,
      toggleSelection,
      isSelected,
      submitMultipleSelection,
      getMultipleSelectionClass,
      submitFillBlanks,
      submitCloze,
      submitOrdering,
      submitMatching,
      parseOptions,
      parseMatchingOptions,
      fillBlankInputRef,
      setFillBlanksInputRef,
      setOrderingInputRef,
      shuffleArray,
      reset
    }
  }
}
</script>

<style scoped>
.question-card {
  max-width: 800px;
  margin: 0 auto;
}

.answer-option {
  user-select: none;
}
</style>
