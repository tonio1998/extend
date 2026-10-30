<div class="word-section-toolbar bg-light border-bottom px-2 py-1 d-flex flex-wrap align-items-center gap-1">
    {{-- Undo / Redo --}}
    <div class="word-ribbon-group">
        <button type="button" class="word-btn" data-cmd="undo" title="Undo (Ctrl+Z)"><i class="mdi mdi-undo"></i></button>
        <button type="button" class="word-btn" data-cmd="redo" title="Redo (Ctrl+Y)"><i class="mdi mdi-redo"></i></button>
    </div>

    {{-- Typography (Font & Size) --}}
    <div class="word-ribbon-group d-none d-sm-flex">
        <select class="form-select form-select-xs border-0 bg-transparent py-0 ps-1 pe-3 text-secondary word-font-family" style="width: 105px; font-size: 11.5px;" title="Font Family">
            <option value="Aptos, Calibri, sans-serif" selected>Aptos</option>
            <option value="Calibri, sans-serif">Calibri</option>
            <option value="'Times New Roman', serif">Times New Roman</option>
            <option value="Arial, sans-serif">Arial</option>
        </select>
        <select class="form-select form-select-xs border-0 bg-transparent py-0 ps-1 pe-3 text-secondary font-mono word-font-size" style="width: 60px; font-size: 11.5px;" title="Font Size">
            <option value="12px">10pt</option>
            <option value="14px" selected>11pt</option>
            <option value="16px">12pt</option>
            <option value="18px">14pt</option>
        </select>
    </div>

    {{-- Formatting --}}
    <div class="word-ribbon-group">
        <button type="button" class="word-btn" data-cmd="bold" title="Bold (Ctrl+B)"><strong>B</strong></button>
        <button type="button" class="word-btn" data-cmd="italic" title="Italic (Ctrl+I)"><em>I</em></button>
        <button type="button" class="word-btn" data-cmd="underline" title="Underline (Ctrl+U)"><u>U</u></button>
        <button type="button" class="word-btn" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>
    </div>

    {{-- Font & Highlight Colors --}}
    <div class="word-ribbon-group">
        <div class="dropdown d-inline-block">
            <button type="button" class="word-btn dropdown-toggle dropdown-toggle-split px-1" data-bs-toggle="dropdown" title="Text Color">
                <i class="mdi mdi-format-color-text" style="border-bottom: 2.5px solid #185abd;"></i>
            </button>
            <div class="dropdown-menu p-2 shadow-sm font-color-palette" style="min-width: 140px;">
                <div class="extra-small fw-bold text-muted mb-1">Text Color</div>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm p-0 rounded-circle border set-text-color-btn" data-color="#0f172a" style="width: 20px; height: 20px; background: #0f172a;" title="Automatic / Dark"></button>
                    <button type="button" class="btn btn-sm p-0 rounded-circle border set-text-color-btn" data-color="#185abd" style="width: 20px; height: 20px; background: #185abd;" title="Office Blue"></button>
                    <button type="button" class="btn btn-sm p-0 rounded-circle border set-text-color-btn" data-color="#dc2626" style="width: 20px; height: 20px; background: #dc2626;" title="Red"></button>
                    <button type="button" class="btn btn-sm p-0 rounded-circle border set-text-color-btn" data-color="#16a34a" style="width: 20px; height: 20px; background: #16a34a;" title="Green"></button>
                    <button type="button" class="btn btn-sm p-0 rounded-circle border set-text-color-btn" data-color="#d97706" style="width: 20px; height: 20px; background: #d97706;" title="Amber"></button>
                </div>
            </div>
        </div>

        <div class="dropdown d-inline-block">
            <button type="button" class="word-btn dropdown-toggle dropdown-toggle-split px-1" data-bs-toggle="dropdown" title="Highlight Color">
                <i class="mdi mdi-format-color-highlight" style="border-bottom: 2.5px solid #facc15;"></i>
            </button>
            <div class="dropdown-menu p-2 shadow-sm highlight-color-palette" style="min-width: 140px;">
                <div class="extra-small fw-bold text-muted mb-1">Highlight</div>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm p-0 rounded-circle border set-highlight-color-btn" data-color="#fef08a" style="width: 20px; height: 20px; background: #fef08a;" title="Yellow"></button>
                    <button type="button" class="btn btn-sm p-0 rounded-circle border set-highlight-color-btn" data-color="#bbf7d0" style="width: 20px; height: 20px; background: #bbf7d0;" title="Bright Green"></button>
                    <button type="button" class="btn btn-sm p-0 rounded-circle border set-highlight-color-btn" data-color="#bae6fd" style="width: 20px; height: 20px; background: #bae6fd;" title="Turquoise"></button>
                    <button type="button" class="btn btn-sm p-0 rounded-circle border set-highlight-color-btn" data-color="#fbcfe8" style="width: 20px; height: 20px; background: #fbcfe8;" title="Pink"></button>
                    <button type="button" class="btn btn-sm p-0 rounded-circle border set-highlight-color-btn d-flex align-items-center justify-content-center" data-color="transparent" style="width: 20px; height: 20px; background: #ffffff;" title="No Color"><i class="mdi mdi-close" style="font-size: 10px;"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Alignment --}}
    <div class="word-ribbon-group">
        <button type="button" class="word-btn" data-cmd="justifyLeft" title="Align Left"><i class="mdi mdi-format-align-left"></i></button>
        <button type="button" class="word-btn" data-cmd="justifyCenter" title="Align Center"><i class="mdi mdi-format-align-center"></i></button>
        <button type="button" class="word-btn" data-cmd="justifyRight" title="Align Right"><i class="mdi mdi-format-align-right"></i></button>
        <button type="button" class="word-btn" data-cmd="justifyFull" title="Justify"><i class="mdi mdi-format-align-justify"></i></button>
    </div>

    {{-- Lists & Indent --}}
    <div class="word-ribbon-group">
        <button type="button" class="word-btn" data-cmd="insertUnorderedList" title="Bullet List"><i class="mdi mdi-format-list-bulleted"></i></button>
        <button type="button" class="word-btn" data-cmd="insertOrderedList" title="Numbered List"><i class="mdi mdi-format-list-numbered"></i></button>
        <button type="button" class="word-btn" data-cmd="outdent" title="Decrease Indent"><i class="mdi mdi-format-indent-decrease"></i></button>
        <button type="button" class="word-btn" data-cmd="indent" title="Increase Indent"><i class="mdi mdi-format-indent-increase"></i></button>
    </div>

    {{-- Clear Formatting --}}
    <div class="word-ribbon-group border-end-0 ms-auto">
        <button type="button" class="word-btn" data-cmd="removeFormat" title="Clear Formatting"><i class="mdi mdi-format-clear"></i></button>
    </div>
</div>
