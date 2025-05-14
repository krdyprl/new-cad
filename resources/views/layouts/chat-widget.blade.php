{{-- Floating chat widget — present on every page. Reuses the real chatbot via ?embed=1 in an iframe
     so there is zero duplication of the chat/AI logic. ponytail: iframe the bare page, don't re-build it. --}}
@php $isId = app()->getLocale() === 'id'; @endphp
<div id="cadChat">
  <button id="cadChatToggle" type="button" aria-label="{{ $isId ? 'Buka obrolan' : 'Open chat' }}" aria-expanded="false">
    <i class="fas fa-comment-dots" aria-hidden="true"></i>
    <span class="cad-chat-dot"></span>
  </button>

  <div id="cadChatPanel" role="dialog" aria-label="{{ $isId ? 'Pemandu Virtual CAD' : 'CAD Virtual Guide' }}" hidden>
    <div class="cad-chat-head">
      <div class="cad-chat-id">
        <span class="cad-chat-ava"><i class="fas fa-robot" aria-hidden="true"></i></span>
        <span>
          <strong>Pemandu CAD</strong>
          <small>{{ $isId ? 'Biasanya membalas cepat' : 'Usually replies quickly' }}</small>
        </span>
      </div>
      <button id="cadChatClose" type="button" aria-label="{{ $isId ? 'Tutup' : 'Close' }}"><i class="fas fa-times" aria-hidden="true"></i></button>
    </div>
    {{-- iframe loaded lazily on first open --}}
    <iframe id="cadChatFrame" title="{{ $isId ? 'Pemandu Virtual CAD' : 'CAD Virtual Guide' }}" data-src="{{ route('frontend.chatbot') }}?embed=1"></iframe>
  </div>
</div>

<style>
  #cadChat { position: fixed; right: 22px; bottom: 22px; z-index: 1080; font-family: "Plus Jakarta Sans", sans-serif; }
  #cadChatToggle {
    position: relative; width: 60px; height: 60px; border-radius: 50%; border: 0; cursor: pointer;
    background: var(--glaze, #2E6E61); color: #fff; font-size: 1.5rem;
    box-shadow: 0 14px 30px -10px rgba(35, 79, 70, .7); transition: transform .2s ease, background .2s ease;
  }
  #cadChatToggle:hover { transform: translateY(-2px) scale(1.05); background: var(--glaze-deep, #234F46); }
  #cadChatToggle .cad-chat-dot {
    position: absolute; top: 6px; right: 6px; width: 12px; height: 12px; border-radius: 50%;
    background: #E5533C; border: 2px solid #fff;
  }
  #cadChat.open #cadChatToggle .cad-chat-dot { display: none; }

  #cadChatPanel {
    position: absolute; right: 0; bottom: 76px; width: min(380px, calc(100vw - 32px)); height: min(560px, calc(100vh - 120px));
    background: #fff; border-radius: 18px; overflow: hidden; display: flex; flex-direction: column;
    box-shadow: 0 28px 60px -18px rgba(33, 26, 21, .55); border: 1px solid var(--line, #E3D9C8);
    transform-origin: bottom right; animation: cadPop .22s ease;
  }
  @keyframes cadPop { from { opacity: 0; transform: translateY(12px) scale(.97); } }
  .cad-chat-head {
    display: flex; align-items: center; justify-content: space-between; gap: .5rem;
    padding: .85rem 1rem; background: linear-gradient(135deg, var(--glaze, #2E6E61), var(--glaze-deep, #234F46)); color: #fff;
  }
  .cad-chat-id { display: flex; align-items: center; gap: .65rem; }
  .cad-chat-id strong { font-family: "Fraunces", serif; font-size: 1rem; display: block; line-height: 1.1; }
  .cad-chat-id small { opacity: .85; font-size: .76rem; }
  .cad-chat-ava {
    width: 38px; height: 38px; border-radius: 50%; background: rgba(255,255,255,.18);
    display: grid; place-items: center; font-size: 1.05rem;
  }
  #cadChatClose { background: transparent; border: 0; color: #fff; font-size: 1.1rem; cursor: pointer; opacity: .85; padding: .25rem .4rem; }
  #cadChatClose:hover { opacity: 1; }
  #cadChatFrame { flex: 1 1 auto; width: 100%; border: 0; background: #fff; }

  @media (prefers-reduced-motion: reduce) { #cadChatPanel { animation: none; } #cadChatToggle { transition: none; } }
  @media (max-width: 480px) { #cadChat { right: 14px; bottom: 14px; } }
</style>

<script>
(function () {
  var wrap = document.getElementById('cadChat');
  var toggle = document.getElementById('cadChatToggle');
  var panel = document.getElementById('cadChatPanel');
  var close = document.getElementById('cadChatClose');
  var frame = document.getElementById('cadChatFrame');
  var loaded = false;

  function open() {
    if (!loaded) { frame.src = frame.dataset.src; loaded = true; } // lazy-load on first open
    panel.hidden = false; wrap.classList.add('open'); toggle.setAttribute('aria-expanded', 'true');
  }
  function hide() { panel.hidden = true; wrap.classList.remove('open'); toggle.setAttribute('aria-expanded', 'false'); }

  toggle.addEventListener('click', function () { panel.hidden ? open() : hide(); });
  close.addEventListener('click', hide);
  addEventListener('keydown', function (e) { if (e.key === 'Escape' && !panel.hidden) hide(); });
})();
</script>
