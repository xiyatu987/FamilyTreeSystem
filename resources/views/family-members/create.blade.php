@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">添加家族成员</h1>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">基本信息</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('family-members.store') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">姓名 *</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="gender" class="form-label">性别 *</label>
                            <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="">请选择</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>男</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>女</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>其他</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="birth_date" class="form-label">出生日期</label>
                            <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="death_date" class="form-label">去世日期</label>
                            <input type="date" name="death_date" id="death_date" class="form-control @error('death_date') is-invalid @enderror" value="{{ old('death_date') }}">
                            @error('death_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="birth_place" class="form-label">出生地</label>
                            <input type="text" name="birth_place" id="birth_place" class="form-control @error('birth_place') is-invalid @enderror" value="{{ old('birth_place') }}">
                            @error('birth_place')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="death_place" class="form-label">去世地</label>
                            <input type="text" name="death_place" id="death_place" class="form-control @error('death_place') is-invalid @enderror" value="{{ old('death_place') }}">
                            @error('death_place')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="ziwei_id" class="form-label">字辈</label>
                            <select name="ziwei_id" id="ziwei_id" class="form-control @error('ziwei_id') is-invalid @enderror">
                                <option value="">请选择</option>
                                @foreach($ziweiList as $ziwei)
                                    <option value="{{ $ziwei->id }}" {{ old('ziwei_id') == $ziwei->id ? 'selected' : '' }}>{{ $ziwei->character }}</option>
                                @endforeach
                            </select>
                            @error('ziwei_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="generation" class="form-label">世代</label>
                            <input type="number" name="generation" id="generation" class="form-control @error('generation') is-invalid @enderror" value="{{ old('generation') }}">
                            @error('generation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">亲属关系</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="father_id" class="form-label">父亲</label>
                                    <select name="father_id" id="father_id" class="form-control @error('father_id') is-invalid @enderror">
                                        <option value="">请选择</option>
                                        @foreach($familyMembers->where('gender', 'male') as $member)
                                            <option value="{{ $member->id }}" {{ old('father_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('father_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mother_id" class="form-label">母亲</label>
                                    <select name="mother_id" id="mother_id" class="form-control @error('mother_id') is-invalid @enderror">
                                        <option value="">请选择</option>
                                        @foreach($familyMembers->where('gender', 'female') as $member)
                                            <option value="{{ $member->id }}" {{ old('mother_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('mother_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="spouse_id" class="form-label">配偶</label>
                            <select name="spouse_id" id="spouse_id" class="form-control @error('spouse_id') is-invalid @enderror">
                                <option value="">请选择</option>
                                @foreach($familyMembers->where('id', '!=', old('father_id'))->where('id', '!=', old('mother_id')) as $member)
                                    <option value="{{ $member->id }}" {{ old('spouse_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                @endforeach
                            </select>
                            @error('spouse_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3 mt-4">
                    <label for="description" class="form-label">备注信息</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">保存</button>
                    <a href="{{ route('family-members.index') }}" class="btn btn-secondary">取消</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection