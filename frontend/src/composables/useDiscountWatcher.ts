import { watch } from 'vue';
import { useUserStore } from '@/store/user';
import { useOfferStore } from '@/store/offer';

export function useDiscountWatcher() {
  const offerStore = useOfferStore();
  const userStore = useUserStore();

  watch(
    () => offerStore.offer.offerDetails.map((detail) => detail.discount),
    (newDiscounts) => {
      if (!userStore.isCreator()) {
        newDiscounts.forEach((discount, index) => {
          if (discount > 10) {
            offerStore.offer.offerDetails[index].discount = 10;
          } else if (discount < -10) {
            offerStore.offer.offerDetails[index].discount = -10;
          }
        });
      }
    },
    { deep: true }
  );

  watch(
    () => offerStore.offer.globalDiscount,
    (newGlobalDiscount) => {
      if (!userStore.isCreator()) {
        if (newGlobalDiscount > 10) {
          offerStore.offer.globalDiscount = 10;
        } else if (newGlobalDiscount < -10) {
          offerStore.offer.globalDiscount = -10;
        }
      }
    }
  );
}
