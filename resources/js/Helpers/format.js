export const formatDate = (date) =>
  new Date(date).toLocaleDateString("en-us", {
    year: "2-digit",
    month: "numeric",
    day: "numeric",
    hour:"2-digit",
    minute:"2-digit",
});

export const formatLongDate = (date) =>
  new Date(date).toLocaleDateString("en-us", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour:"2-digit",
    minute:"2-digit",
});


export const formatUSD = (value)=>{
    return new Intl.NumberFormat('en-US', {style: 'currency', currency: 'USD'})
      .format(value);
  }
  