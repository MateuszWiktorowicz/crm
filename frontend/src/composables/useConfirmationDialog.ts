// composables/useConfirmationDialog.ts
import { ref } from 'vue';
import type { ComponentPublicInstance } from 'vue';
import ConfirmationDialog from '@/components/ConfirmationDialog.vue';

type ConfirmationDialogComponent = ComponentPublicInstance<{
  open: (message: string) => Promise<boolean>;
}>;

const dialogRef = ref<ConfirmationDialogComponent | null>(null);

export function useConfirmationDialog() {
  return {
    dialogRef,
    showConfirmationDialog: (message: string) => {
      return dialogRef.value?.open(message);
    },
    ConfirmationDialog,
  };
}
