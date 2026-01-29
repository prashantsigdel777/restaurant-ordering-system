async function postForm(url, data) {
  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams(data),
  });
  return res.json();
}

function setCartCount(count) {
  const el = document.getElementById("cartCount");
  if (el) el.textContent = count;
}

document.addEventListener("click", async (e) => {
  const btn = e.target.closest("[data-add-to-cart]");
  if (!btn) return;

  const itemId = btn.getAttribute("data-add-to-cart");
  const oldText = btn.textContent;

  btn.disabled = true;
  btn.textContent = "Adding...";

  try {
    const out = await postForm("/restaurant/ajax/add_to_cart.php", {
      item_id: itemId,
      qty: 1,
    });

    if (out.ok) {
      setCartCount(out.cartCount); // ✅ updates navbar immediately
      btn.textContent = "Added ✓";
      setTimeout(() => {
        btn.textContent = oldText;
        btn.disabled = false;
      }, 600);
    } else {
      alert(out.error || "Failed to add to cart");
      btn.textContent = oldText;
      btn.disabled = false;
    }
  } catch (err) {
    alert("Network error");
    btn.textContent = oldText;
    btn.disabled = false;
  }
});
