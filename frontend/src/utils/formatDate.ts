export function formatDate(date: string) {
  const formatedDate: Date = new Date(date);
  const day = String(formatedDate.getDate()).padStart(2, '0');
  const month = String(formatedDate.getMonth() + 1).padStart(2, '0');
  const year = formatedDate.getFullYear();

  return `${day}/${month}/${year}`;
}
