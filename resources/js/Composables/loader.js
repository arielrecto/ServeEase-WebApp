import { ref } from 'vue';

const isLoading = ref(false);

export const useLoader = () => {
    const setIsLoading = (state) => {
        isLoading.value = state;
    }

    return { isLoading, setIsLoading };
};