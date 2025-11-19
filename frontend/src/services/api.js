import axios from 'axios'

// Configuração base do Axios
const api = axios.create({
  baseURL: '/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Interceptor para adicionar token de autenticação (quando implementado)
api.interceptors.request.use(
  (config) => {
    // Futuramente: adicionar token JWT aqui
    // const token = localStorage.getItem('token')
    // if (token) {
    //   config.headers.Authorization = `Bearer ${token}`
    // }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Interceptor para tratar erros globalmente
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      // Erro de resposta do servidor
      console.error('API Error:', error.response.data)
    } else if (error.request) {
      // Erro de requisição (sem resposta)
      console.error('Network Error:', error.request)
    } else {
      // Outro tipo de erro
      console.error('Error:', error.message)
    }
    return Promise.reject(error)
  }
)

// Serviços de Exames
export const examService = {
  getAll: () => api.get('/exams'),
  getById: (id) => api.get(`/exams/${id}`),
  getQuestions: (id) => api.get(`/exams/${id}/questions`),
  create: (data) => api.post('/exams', data),
  update: (id, data) => api.put(`/exams/${id}`, data),
  delete: (id) => api.delete(`/exams/${id}`)
}

// Serviços de Categorias
export const categoryService = {
  getAll: () => api.get('/categories'),
  getById: (id) => api.get(`/categories/${id}`),
  getQuestions: (id) => api.get(`/categories/${id}/questions`),
  create: (data) => api.post('/categories', data),
  update: (id, data) => api.put(`/categories/${id}`, data),
  delete: (id) => api.delete(`/categories/${id}`)
}

// Serviços de Questões
export const questionService = {
  getAll: (params) => api.get('/questions', { params }),
  getById: (id) => api.get(`/questions/${id}`),
  answer: (id, data) => api.post(`/questions/${id}/answer`, data),
  create: (data) => api.post('/questions', data),
  update: (id, data) => api.put(`/questions/${id}`, data),
  delete: (id) => api.delete(`/questions/${id}`)
}

// Serviços de Progresso do Usuário
export const progressService = {
  getProgress: (userId) => api.get('/user/progress', { params: { user_id: userId } }),
  getStatistics: (userId) => api.get('/user/statistics', { params: { user_id: userId } }),
  deleteProgress: (id) => api.delete(`/user/progress/${id}`)
}

export default api
