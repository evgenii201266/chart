    <div class="col-xs-12">
        <div class="form-group buttons">
            <button type="submit" class="btn btn-success save-changes legitRipple">
                <i class="icon-spinner4 spinner position-left save-load-changes hidden"></i>
                Сохранить {{ !empty($groupTab) ? 'изменения' : 'и закрыть' }}
            </button>
            @if (empty($groupTab))
                <a id="form-cancel" href="{{ $mainPath }}" class="btn btn-danger">Отменить</a>
            @endif
        </div>
    </div>
</form>