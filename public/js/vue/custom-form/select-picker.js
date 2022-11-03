export default {
    template: '<select v-model="name" class="form-control" @change="function">' +
    '<option v-for="option in options" value="{{ option.value }}">{{{ option.text }}}</option>' +
    '</select>',
    name: 'selectpicker',
    props: ['options', 'name', 'function'],
    created () {
        // Init our picker
        $(this.$el).selectpicker({
            iconBase: 'fa',
            tickIcon: 'fa-check'
        });
        // Update whenever options change
        this.$watch('options', function (val) {
            // Refresh our picker UI
            $(this.$el).selectpicker('refresh');
            // Update manually because v-model won't catch
            this.name = $(this.$el).selectpicker('val');
        }.bind(this))
    }
}


(function (t, a) {
    typeof exports == "object" && typeof module != "undefined" ? module.exports = a(require("vue")) : typeof define == "function" && define.amd ? define(["vue"], a) : (t = typeof globalThis != "undefined" ? globalThis : t || self, t["vue-select"] = a(t.Vue))
})(this, function (t) {
    "use strict";
    var X = Object.defineProperty, Y = Object.defineProperties;
    var Q = Object.getOwnPropertyDescriptors;
    var y = Object.getOwnPropertySymbols;
    var G = Object.prototype.hasOwnProperty, W = Object.prototype.propertyIsEnumerable;
    var b = (t, a, d)=>a in t ? X(t, a, {enumerable: !0, configurable: !0, writable: !0, value: d}) : t[a] = d, h = (t, a)=> {
        for (var d in a || (a = {}))G.call(a, d) && b(t, d, a[d]);
        if (y)for (var d of y(a))W.call(a, d) && b(t, d, a[d]);
        return t
    }, m = (t, a)=>Y(t, Q(a));
    const a = {
        props: {autoscroll: {type: Boolean, default: !0}}, watch: {
            typeAheadPointer(){
                this.autoscroll && this.maybeAdjustScroll()
            }, open(e){
                this.autoscroll && e && this.$nextTick(()=>this.maybeAdjustScroll())
            }
        }, methods: {
            maybeAdjustScroll(){
                var s;
                const e = ((s = this.$refs.dropdownMenu) == null ? void 0 : s.children[this.typeAheadPointer]) || !1;
                if (e) {
                    const i = this.getDropdownViewport(), {top:l,bottom:r,height:o}=e.getBoundingClientRect();
                    if (l < i.top)return this.$refs.dropdownMenu.scrollTop = e.offsetTop;
                    if (r > i.bottom)return this.$refs.dropdownMenu.scrollTop = e.offsetTop - (i.height - o)
                }
            }, getDropdownViewport(){
                return this.$refs.dropdownMenu ? this.$refs.dropdownMenu.getBoundingClientRect() : {height: 0, top: 0, bottom: 0}
            }
        }
    }, d = {
        data(){
            return {typeAheadPointer: -1}
        }, watch: {
            filteredOptions(){
                for (let e = 0; e < this.filteredOptions.length; e++)if (this.selectable(this.filteredOptions[e])) {
                    this.typeAheadPointer = e;
                    break
                }
            }, open(e){
                e && this.typeAheadToLastSelected()
            }, selectedValue(){
                this.open && this.typeAheadToLastSelected()
            }
        }, methods: {
            typeAheadUp(){
                for (let e = this.typeAheadPointer - 1; e >= 0; e--)if (this.selectable(this.filteredOptions[e])) {
                    this.typeAheadPointer = e;
                    break
                }
            }, typeAheadDown(){
                for (let e = this.typeAheadPointer + 1; e < this.filteredOptions.length; e++)if (this.selectable(this.filteredOptions[e])) {
                    this.typeAheadPointer = e;
                    break
                }
            }, typeAheadSelect(){
                const e = this.filteredOptions[this.typeAheadPointer];
                e && this.selectable(e) && this.select(e)
            }, typeAheadToLastSelected(){
                this.typeAheadPointer = this.selectedValue.length !== 0 ? this.filteredOptions.indexOf(this.selectedValue[this.selectedValue.length - 1]) : -1
            }
        }
    }, _ = {
        props: {loading: {type: Boolean, default: !1}}, data(){
            return {mutableLoading: !1}
        }, watch: {
            search(){
                this.$emit("search", this.search, this.toggleLoading)
            }, loading(e){
                this.mutableLoading = e
            }
        }, methods: {
            toggleLoading(e = null){
                return e == null ? this.mutableLoading = !this.mutableLoading : this.mutableLoading = e
            }
        }
    }, u = (e, s)=> {
        const i = e.__vccOpts || e;
        for (const [l,r]of s)i[l] = r;
        return i
    }, O = {}, w = {
        xmlns: "http://www.w3.org/2000/svg",
        width: "10",
        height: "10"
    }, S = [t.createElementVNode("path", {d: "M6.895455 5l2.842897-2.842898c.348864-.348863.348864-.914488 0-1.263636L9.106534.261648c-.348864-.348864-.914489-.348864-1.263636 0L5 3.104545 2.157102.261648c-.348863-.348864-.914488-.348864-1.263636 0L.261648.893466c-.348864.348864-.348864.914489 0 1.263636L3.104545 5 .261648 7.842898c-.348864.348863-.348864.914488 0 1.263636l.631818.631818c.348864.348864.914773.348864 1.263636 0L5 6.895455l2.842898 2.842897c.348863.348864.914772.348864 1.263636 0l.631818-.631818c.348864-.348864.348864-.914489 0-1.263636L6.895455 5z"}, null, -1)];

    function V(e, s) {
        return t.openBlock(), t.createElementBlock("svg", w, S)
    }

    const B = u(O, [["render", V]]), k = {}, C = {
        xmlns: "http://www.w3.org/2000/svg",
        width: "14",
        height: "10"
    }, P = [t.createElementVNode("path", {d: "M9.211364 7.59931l4.48338-4.867229c.407008-.441854.407008-1.158247 0-1.60046l-.73712-.80023c-.407008-.441854-1.066904-.441854-1.474243 0L7 5.198617 2.51662.33139c-.407008-.441853-1.066904-.441853-1.474243 0l-.737121.80023c-.407008.441854-.407008 1.158248 0 1.600461l4.48338 4.867228L7 10l2.211364-2.40069z"}, null, -1)];

    function A(e, s) {
        return t.openBlock(), t.createElementBlock("svg", C, P)
    }

    const g = {Deselect: B, OpenIndicator: u(k, [["render", A]])}, L = {
        mounted(e, {instance:s}){
            if (s.appendToBody) {
                const {height:i,top:l,left:r,width:o}=s.$refs.toggle.getBoundingClientRect();
                let f = window.scrollX || window.pageXOffset, n = window.scrollY || window.pageYOffset;
                e.unbindPosition = s.calculatePosition(e, s, {width: o + "px", left: f + r + "px", top: n + l + i + "px"}), document.body.appendChild(e)
            }
        }, unmounted(e, {instance:s}){
            s.appendToBody && (e.unbindPosition && typeof e.unbindPosition == "function" && e.unbindPosition(), e.parentNode && e.parentNode.removeChild(e))
        }
    };

    function D(e) {
        const s = {};
        return Object.keys(e).sort().forEach(i=> {
            s[i] = e[i]
        }), JSON.stringify(s)
    }

    let E = 0;

    function T() {
        return ++E
    }

    const te = "", F = {
        components: h({}, g),
        directives: {appendToBody: L},
        mixins: [a, d, _],
        compatConfig: {MODE: 3},
        emits: ["open", "close", "update:modelValue", "search", "search:compositionstart", "search:compositionend", "search:keydown", "search:blur", "search:focus", "search:input", "option:created", "option:selecting", "option:selected", "option:deselecting", "option:deselected"],
        props: {
            modelValue: {},
            components: {type: Object, default: ()=>({})},
            options: {
                type: Array, default(){
                    return []
                }
            },
            disabled: {type: Boolean, default: !1},
            clearable: {type: Boolean, default: !0},
            deselectFromDropdown: {type: Boolean, default: !1},
            searchable: {type: Boolean, default: !0},
            multiple: {type: Boolean, default: !1},
            placeholder: {type: String, default: ""},
            transition: {type: String, default: "vs__fade"},
            clearSearchOnSelect: {type: Boolean, default: !0},
            closeOnSelect: {type: Boolean, default: !0},
            label: {type: String, default: "label"},
            autocomplete: {type: String, default: "off"},
            reduce: {type: Function, default: e=>e},
            selectable: {type: Function, default: e=>!0},
            getOptionLabel: {
                type: Function, default(e){
                    return typeof e == "object" ? e.hasOwnProperty(this.label) ? e[this.label] : console.warn(`[vue-select warn]: Label key "option.${this.label}" does not exist in options object ${JSON.stringify(e)}.
https://vue-select.org/api/props.html#getoptionlabel`) : e
                }
            },
            getOptionKey: {
                type: Function, default(e){
                    if (typeof e != "object")return e;
                    try {
                        return e.hasOwnProperty("id") ? e.id : D(e)
                    } catch (s) {
                        return console.warn(`[vue-select warn]: Could not stringify this option to generate unique key. Please provide'getOptionKey' prop to return a unique key for each option.
https://vue-select.org/api/props.html#getoptionkey`, e, s)
                    }
                }
            },
            onTab: {
                type: Function, default: function () {
                    this.selectOnTab && !this.isComposing && this.typeAheadSelect()
                }
            },
            taggable: {type: Boolean, default: !1},
            tabindex: {type: Number, default: null},
            pushTags: {type: Boolean, default: !1},
            filterable: {type: Boolean, default: !0},
            filterBy: {
                type: Function, default(e, s, i){
                    return (s || "").toLocaleLowerCase().indexOf(i.toLocaleLowerCase()) > -1
                }
            },
            filter: {
                type: Function, default(e, s){
                    return e.filter(i=> {
                        let l = this.getOptionLabel(i);
                        return typeof l == "number" && (l = l.toString()), this.filterBy(i, l, s)
                    })
                }
            },
            createOption: {
                type: Function, default(e){
                    return typeof this.optionList[0] == "object" ? {[this.label]: e} : e
                }
            },
            resetOnOptionsChange: {default: !1, validator: e=>["function", "boolean"].includes(typeof e)},
            clearSearchOnBlur: {
                type: Function, default: function ({clearSearchOnSelect:e,multiple:s}) {
                    return e && !s
                }
            },
            noDrop: {type: Boolean, default: !1},
            inputId: {type: String},
            dir: {type: String, default: "auto"},
            selectOnTab: {type: Boolean, default: !1},
            selectOnKeyCodes: {type: Array, default: ()=>[13]},
            searchInputQuerySelector: {type: String, default: "[type=search]"},
            mapKeydown: {type: Function, default: (e, s)=>e},
            appendToBody: {type: Boolean, default: !1},
            calculatePosition: {
                type: Function, default(e, s, {width:i,top:l,left:r}){
                    e.style.top = l, e.style.left = r, e.style.width = i
                }
            },
            dropdownShouldOpen: {
                type: Function, default({noDrop:e,open:s,mutableLoading:i}){
                    return e ? !1 : s && !i
                }
            },
            uid: {type: [String, Number], default: ()=>T()}
        },
        data(){
            return {search: "", open: !1, isComposing: !1, pushedTags: [], _value: [], deselectButtons: []}
        },
        computed: {
            isReducingValues(){
                return this.$props.reduce !== this.$options.props.reduce.default
            }, isTrackingValues(){
                return typeof this.modelValue == "undefined" || this.isReducingValues
            }, selectedValue(){
                let e = this.modelValue;
                return this.isTrackingValues && (e = this.$data._value), e != null && e !== "" ? [].concat(e) : []
            }, optionList(){
                return this.options.concat(this.pushTags ? this.pushedTags : [])
            }, searchEl(){
                return this.$slots.search ? this.$refs.selectedOptions.querySelector(this.searchInputQuerySelector) : this.$refs.search
            }, scope(){
                const e = {search: this.search, loading: this.loading, searching: this.searching, filteredOptions: this.filteredOptions};
                return {
                    search: {
                        attributes: h({
                            disabled: this.disabled,
                            placeholder: this.searchPlaceholder,
                            tabindex: this.tabindex,
                            readonly: !this.searchable,
                            id: this.inputId,
                            "aria-autocomplete": "list",
                            "aria-labelledby": `vs${this.uid}__combobox`,
                            "aria-controls": `vs${this.uid}__listbox`,
                            ref: "search",
                            type: "search",
                            autocomplete: this.autocomplete,
                            value: this.search
                        }, this.dropdownOpen && this.filteredOptions[this.typeAheadPointer] ? {"aria-activedescendant": `vs${this.uid}__option-${this.typeAheadPointer}`} : {}),
                        events: {compositionstart: ()=>this.isComposing = !0, compositionend: ()=>this.isComposing = !1, keydown: this.onSearchKeyDown, blur: this.onSearchBlur, focus: this.onSearchFocus, input: s=>this.search = s.target.value}
                    },
                    spinner: {loading: this.mutableLoading},
                    noOptions: {search: this.search, loading: this.mutableLoading, searching: this.searching},
                    openIndicator: {attributes: {ref: "openIndicator", role: "presentation", class: "vs__open-indicator"}},
                    listHeader: e,
                    listFooter: e,
                    header: m(h({}, e), {deselect: this.deselect}),
                    footer: m(h({}, e), {deselect: this.deselect})
                }
            }, childComponents(){
                return h(h({}, g), this.components)
            }, stateClasses(){
                return {
                    "vs--open": this.dropdownOpen,
                    "vs--single": !this.multiple,
                    "vs--multiple": this.multiple,
                    "vs--searching": this.searching && !this.noDrop,
                    "vs--searchable": this.searchable && !this.noDrop,
                    "vs--unsearchable": !this.searchable,
                    "vs--loading": this.mutableLoading,
                    "vs--disabled": this.disabled
                }
            }, searching(){
                return !!this.search
            }, dropdownOpen(){
                return this.dropdownShouldOpen(this)
            }, searchPlaceholder(){
                return this.isValueEmpty && this.placeholder ? this.placeholder : void 0
            }, filteredOptions(){
                const e = [].concat(this.optionList);
                if (!this.filterable && !this.taggable)return e;
                const s = this.search.length ? this.filter(e, this.search, this) : e;
                if (this.taggable && this.search.length) {
                    const i = this.createOption(this.search);
                    this.optionExists(i) || s.unshift(i)
                }
                return s
            }, isValueEmpty(){
                return this.selectedValue.length === 0
            }, showClearButton(){
                return !this.multiple && this.clearable && !this.open && !this.isValueEmpty
            }
        },
        watch: {
            options(e, s){
                const i = ()=>typeof this.resetOnOptionsChange == "function" ? this.resetOnOptionsChange(e, s, this.selectedValue) : this.resetOnOptionsChange;
                !this.taggable && i() && this.clearSelection(), this.modelValue && this.isTrackingValues && this.setInternalValueFromOptions(this.modelValue)
            }, modelValue: {
                immediate: !0, handler(e){
                    this.isTrackingValues && this.setInternalValueFromOptions(e)
                }
            }, multiple(){
                this.clearSelection()
            }, open(e){
                this.$emit(e ? "open" : "close")
            }
        },
        created(){
            this.mutableLoading = this.loading
        },
        methods: {
            setInternalValueFromOptions(e){
                Array.isArray(e) ? this.$data._value = e.map(s=>this.findOptionFromReducedValue(s)) : this.$data._value = this.findOptionFromReducedValue(e)
            }, select(e){
                this.$emit("option:selecting", e), this.isOptionSelected(e) ? this.deselectFromDropdown && (this.clearable || this.multiple && this.selectedValue.length > 1) && this.deselect(e) : (this.taggable && !this.optionExists(e) && (this.$emit("option:created", e), this.pushTag(e)), this.multiple && (e = this.selectedValue.concat(e)), this.updateValue(e), this.$emit("option:selected", e)), this.onAfterSelect(e)
            }, deselect(e){
                this.$emit("option:deselecting", e), this.updateValue(this.selectedValue.filter(s=>!this.optionComparator(s, e))), this.$emit("option:deselected", e)
            }, clearSelection(){
                this.updateValue(this.multiple ? [] : null)
            }, onAfterSelect(e){
                this.closeOnSelect && (this.open = !this.open, this.searchEl.blur()), this.clearSearchOnSelect && (this.search = "")
            }, updateValue(e){
                typeof this.modelValue == "undefined" && (this.$data._value = e), e !== null && (Array.isArray(e) ? e = e.map(s=>this.reduce(s)) : e = this.reduce(e)), this.$emit("update:modelValue", e)
            }, toggleDropdown(e){
                const s = e.target !== this.searchEl;
                s && e.preventDefault();
                const i = [...this.deselectButtons || [], this.$refs.clearButton];
                if (this.searchEl === void 0 || i.filter(Boolean).some(l=>l.contains(e.target) || l === e.target)) {
                    e.preventDefault();
                    return
                }
                this.open && s ? this.searchEl.blur() : this.disabled || (this.open = !0, this.searchEl.focus())
            }, isOptionSelected(e){
                return this.selectedValue.some(s=>this.optionComparator(s, e))
            }, isOptionDeselectable(e){
                return this.isOptionSelected(e) && this.deselectFromDropdown
            }, optionComparator(e, s){
                return this.getOptionKey(e) === this.getOptionKey(s)
            }, findOptionFromReducedValue(e){
                const s = l=>JSON.stringify(this.reduce(l)) === JSON.stringify(e), i = [...this.options, ...this.pushedTags].filter(s);
                return i.length === 1 ? i[0] : i.find(l=>this.optionComparator(l, this.$data._value)) || e
            }, closeSearchOptions(){
                this.open = !1, this.$emit("search:blur")
            }, maybeDeleteValue(){
                if (!this.searchEl.value.length && this.selectedValue && this.selectedValue.length && this.clearable) {
                    let e = null;
                    this.multiple && (e = [...this.selectedValue.slice(0, this.selectedValue.length - 1)]), this.updateValue(e)
                }
            }, optionExists(e){
                return this.optionList.some(s=>this.optionComparator(s, e))
            }, normalizeOptionForSlot(e){
                return typeof e == "object" ? e : {[this.label]: e}
            }, pushTag(e){
                this.pushedTags.push(e)
            }, onEscape(){
                this.search.length ? this.search = "" : this.searchEl.blur()
            }, onSearchBlur(){
                if (this.mousedown && !this.searching)this.mousedown = !1; else {
                    const {clearSearchOnSelect:e,multiple:s}=this;
                    this.clearSearchOnBlur({clearSearchOnSelect: e, multiple: s}) && (this.search = ""), this.closeSearchOptions();
                    return
                }
                if (this.search.length === 0 && this.options.length === 0) {
                    this.closeSearchOptions();
                    return
                }
            }, onSearchFocus(){
                this.open = !0, this.$emit("search:focus")
            }, onMousedown(){
                this.mousedown = !0
            }, onMouseUp(){
                this.mousedown = !1
            }, onSearchKeyDown(e){
                const s = r=>(r.preventDefault(), !this.isComposing && this.typeAheadSelect()), i = {8: r=>this.maybeDeleteValue(), 9: r=>this.onTab(), 27: r=>this.onEscape(), 38: r=>(r.preventDefault(), this.typeAheadUp()), 40: r=>(r.preventDefault(), this.typeAheadDown())};
                this.selectOnKeyCodes.forEach(r=>i[r] = s);
                const l = this.mapKeydown(i, this);
                if (typeof l[e.keyCode] == "function")return l[e.keyCode](e)
            }
        }
    }, N = ["dir"], M = ["id", "aria-expanded", "aria-owns"], x = {ref: "selectedOptions", class: "vs__selected-options"}, z = ["disabled", "title", "aria-label", "onClick"], R = {
        ref: "actions",
        class: "vs__actions"
    }, I = ["disabled"], $ = {class: "vs__spinner"}, j = ["id"], K = ["id", "aria-selected", "onMouseover", "onClick"], q = {key: 0, class: "vs__no-options"}, U = t.createTextVNode(" Sorry, no matching options. "), J = ["id"];

    function H(e, s, i, l, r, o) {
        const f = t.resolveDirective("append-to-body");
        return t.openBlock(), t.createElementBlock("div", {dir: i.dir, class: t.normalizeClass(["v-select", o.stateClasses])}, [t.renderSlot(e.$slots, "header", t.normalizeProps(t.guardReactiveProps(o.scope.header))), t.createElementVNode("div", {
            id: `vs${i.uid}__combobox`,
            ref: "toggle",
            class: "vs__dropdown-toggle",
            role: "combobox",
            "aria-expanded": o.dropdownOpen.toString(),
            "aria-owns": `vs${i.uid}__listbox`,
            "aria-label": "Search for option",
            onMousedown: s[1] || (s[1] = n=>o.toggleDropdown(n))
        }, [t.createElementVNode("div", x, [(t.openBlock(!0), t.createElementBlock(t.Fragment, null, t.renderList(o.selectedValue, (n, c)=>t.renderSlot(e.$slots, "selected-option-container", {
            option: o.normalizeOptionForSlot(n),
            deselect: o.deselect,
            multiple: i.multiple,
            disabled: i.disabled
        }, ()=>[(t.openBlock(), t.createElementBlock("span", {
            key: i.getOptionKey(n),
            class: "vs__selected"
        }, [t.renderSlot(e.$slots, "selected-option", t.normalizeProps(t.guardReactiveProps(o.normalizeOptionForSlot(n))), ()=>[t.createTextVNode(t.toDisplayString(i.getOptionLabel(n)), 1)]), i.multiple ? (t.openBlock(), t.createElementBlock("button", {
            key: 0,
            ref_for: !0,
            ref: p=>r.deselectButtons[c] = p,
            disabled: i.disabled,
            type: "button",
            class: "vs__deselect",
            title: `Deselect ${i.getOptionLabel(n)}`,
            "aria-label": `Deselect ${i.getOptionLabel(n)}`,
            onClick: p=>o.deselect(n)
        }, [(t.openBlock(), t.createBlock(t.resolveDynamicComponent(o.childComponents.Deselect)))], 8, z)) : t.createCommentVNode("", !0)]))])), 256)), t.renderSlot(e.$slots, "search", t.normalizeProps(t.guardReactiveProps(o.scope.search)), ()=>[t.createElementVNode("input", t.mergeProps({class: "vs__search"}, o.scope.search.attributes, t.toHandlers(o.scope.search.events)), null, 16)])], 512), t.createElementVNode("div", R, [t.withDirectives(t.createElementVNode("button", {
            ref: "clearButton",
            disabled: i.disabled,
            type: "button",
            class: "vs__clear",
            title: "Clear Selected",
            "aria-label": "Clear Selected",
            onClick: s[0] || (s[0] = (...n)=>o.clearSelection && o.clearSelection(...n))
        }, [(t.openBlock(), t.createBlock(t.resolveDynamicComponent(o.childComponents.Deselect)))], 8, I), [[t.vShow, o.showClearButton]]), t.renderSlot(e.$slots, "open-indicator", t.normalizeProps(t.guardReactiveProps(o.scope.openIndicator)), ()=>[i.noDrop ? t.createCommentVNode("", !0) : (t.openBlock(), t.createBlock(t.resolveDynamicComponent(o.childComponents.OpenIndicator), t.normalizeProps(t.mergeProps({key: 0}, o.scope.openIndicator.attributes)), null, 16))]), t.renderSlot(e.$slots, "spinner", t.normalizeProps(t.guardReactiveProps(o.scope.spinner)), ()=>[t.withDirectives(t.createElementVNode("div", $, "Loading...", 512), [[t.vShow, e.mutableLoading]])])], 512)], 40, M), t.createVNode(t.Transition, {name: i.transition}, {
            default: t.withCtx(()=>[o.dropdownOpen ? t.withDirectives((t.openBlock(), t.createElementBlock("ul", {
                id: `vs${i.uid}__listbox`,
                ref: "dropdownMenu",
                key: `vs${i.uid}__listbox`,
                class: "vs__dropdown-menu",
                role: "listbox",
                tabindex: "-1",
                onMousedown: s[2] || (s[2] = t.withModifiers((...n)=>o.onMousedown && o.onMousedown(...n), ["prevent"])),
                onMouseup: s[3] || (s[3] = (...n)=>o.onMouseUp && o.onMouseUp(...n))
            }, [t.renderSlot(e.$slots, "list-header", t.normalizeProps(t.guardReactiveProps(o.scope.listHeader))), (t.openBlock(!0), t.createElementBlock(t.Fragment, null, t.renderList(o.filteredOptions, (n, c)=>(t.openBlock(), t.createElementBlock("li", {
                id: `vs${i.uid}__option-${c}`,
                key: i.getOptionKey(n),
                role: "option",
                class: t.normalizeClass(["vs__dropdown-option", {
                    "vs__dropdown-option--deselect": o.isOptionDeselectable(n) && c === e.typeAheadPointer,
                    "vs__dropdown-option--selected": o.isOptionSelected(n),
                    "vs__dropdown-option--highlight": c === e.typeAheadPointer,
                    "vs__dropdown-option--disabled": !i.selectable(n)
                }]),
                "aria-selected": c === e.typeAheadPointer ? !0 : null,
                onMouseover: p=>i.selectable(n) ? e.typeAheadPointer = c : null,
                onClick: t.withModifiers(p=>i.selectable(n) ? o.select(n) : null, ["prevent", "stop"])
            }, [t.renderSlot(e.$slots, "option", t.normalizeProps(t.guardReactiveProps(o.normalizeOptionForSlot(n))), ()=>[t.createTextVNode(t.toDisplayString(i.getOptionLabel(n)), 1)])], 42, K))), 128)), o.filteredOptions.length === 0 ? (t.openBlock(), t.createElementBlock("li", q, [t.renderSlot(e.$slots, "no-options", t.normalizeProps(t.guardReactiveProps(o.scope.noOptions)), ()=>[U])])) : t.createCommentVNode("", !0), t.renderSlot(e.$slots, "list-footer", t.normalizeProps(t.guardReactiveProps(o.scope.listFooter)))], 40, j)), [[f]]) : (t.openBlock(), t.createElementBlock("ul", {
                key: 1,
                id: `vs${i.uid}__listbox`,
                role: "listbox",
                style: {display: "none", visibility: "hidden"}
            }, null, 8, J))]), _: 3
        }, 8, ["name"]), t.renderSlot(e.$slots, "footer", t.normalizeProps(t.guardReactiveProps(o.scope.footer)))], 10, N)
    }

    return u(F, [["render", H]])
});