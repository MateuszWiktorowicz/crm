import axios from 'axios';
import router from './router';


const axiosClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  withCredentials: true,
  withXSRFToken: true,
});

// Track CSRF cookie fetch to avoid concurrent requests
let csrfCookiePromise: Promise<any> | null = null;

// Interceptor to ensure CSRF cookie is fetched before state-changing requests
axiosClient.interceptors.request.use(
  async (config) => {
    // Only fetch CSRF cookie for state-changing methods and exclude the CSRF cookie endpoint itself
    const stateChangingMethods = ['post', 'put', 'patch', 'delete'];
    const isStateChangingRequest = stateChangingMethods.includes(config.method?.toLowerCase() || '');
    const isCsrfCookieRequest = config.url === '/sanctum/csrf-cookie' || config.url?.endsWith('/sanctum/csrf-cookie');

    if (isStateChangingRequest && !isCsrfCookieRequest) {
      // Use existing promise if already fetching, otherwise create a new one
      if (!csrfCookiePromise) {
        csrfCookiePromise = axiosClient.get('/sanctum/csrf-cookie').finally(() => {
          csrfCookiePromise = null;
        });
      }
      // Wait for CSRF cookie to be fetched
      await csrfCookiePromise;
    }

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// axiosClient.interceptors.request.use(config => {
//     config.headers.Authorization = `Bearer ${localStorage.getItem('token')}`;
// });

axiosClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response && error.response.status === 401) {
      router.push({ name: 'Login' });
    }
    throw error;
  }
);

export default axiosClient;
