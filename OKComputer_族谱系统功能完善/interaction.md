# 族谱管理系统 - 完整项目打包

以下是完整的族谱管理系统项目文件结构，您可以直接复制这些代码来创建项目：

## 📁 项目结构

```
族谱管理系统/
├── frontend/                  # 前端文件
│   ├── public/               # 公共资源
│   │   ├── index.html
│   │   ├── members.html
│   │   ├── gallery.html
│   │   ├── blogs.html
│   │   ├── admin.html
│   │   └── assets/
│   │       ├── css/
│   │       │   └── style.css
│   │       ├── js/
│   │       │   ├── main.js
│   │       │   ├── chart-manager.js
│   │       │   ├── data-manager.js
│   │       │   ├── ui-manager.js
│   │       │   └── auth-manager.js
│   │       └── images/
│   │           ├── default-avatar.png
│   │           ├── hero-bg.jpg
│   │           └── logo.png
│   └── package.json
├── backend/                   # 后端文件
│   ├── src/
│   │   ├── server.js
│   │   ├── database.js
│   │   ├── auth.js
│   │   ├── routes/
│   │   │   ├── members.js
│   │   │   ├── photos.js
│   │   │   ├── blogs.js
│   │   │   └── auth.js
│   │   ├── middleware/
│   │   │   ├── auth.js
│   │   │   └── validation.js
│   │   └── models/
│   │       ├── Member.js
│   │       ├── Photo.js
│   │       └── Blog.js
│   ├── package.json
│   ├── .env.example
│   └── uploads/
│       └── .gitkeep
├── database/                  # 数据库文件
│   ├── init.sql
│   └── migrations/
│       └── 001_initial.sql
├── docs/                      # 文档
│   ├── design.md
│   ├── interaction.md
│   └── api.md
├── tests/                     # 测试文件
│   ├── unit/
│   └── integration/
├── docker-compose.yml         # Docker配置
├── dockerfile                 # Docker配置
├── nginx.conf                 # Nginx配置
├── README.md                  # 项目说明
└── .gitignore
```

## 📄 完整文件内容

### 1. 前端文件

#### `frontend/public/index.html`

```html
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>家族树 - 族谱管理系统</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- ECharts -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    
    <!-- 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- 自定义样式 -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <style>
        #family-tree {
            width: 100%;
            height: 600px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            padding: 20px;
        }
        
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        
        .modal-content {
            position: relative;
            background: white;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- 导航栏 -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <i class="fas fa-tree text-green-600 text-2xl mr-2"></i>
                    <span class="text-xl font-bold text-gray-800">家族树系统</span>
                </div>
                
                <div class="hidden md:flex space-x-8">
                    <a href="index.html" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-1">
                        <i class="fas fa-home mr-2"></i>家族树
                    </a>
                    <a href="members.html" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-users mr-2"></i>成员管理
                    </a>
                    <a href="gallery.html" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-images mr-2"></i>家族相册
                    </a>
                    <a href="blogs.html" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-blog mr-2"></i>家族博客
                    </a>
                    <a href="admin.html" class="text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-cog mr-2"></i>管理
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div id="user-info" class="hidden">
                        <span class="text-gray-700" id="username"></span>
                        <button id="logout-btn" class="ml-4 text-red-600 hover:text-red-800">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </div>
                    <button id="login-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>登录
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- 主要内容区域 -->
    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- 家族树区域 -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-sitemap mr-2 text-blue-600"></i>家族树
                        </h2>
                        <div class="flex space-x-2">
                            <button id="zoom-in" class="p-2 rounded hover:bg-gray-100">
                                <i class="fas fa-search-plus"></i>
                            </button>
                            <button id="zoom-out" class="p-2 rounded hover:bg-gray-100">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <button id="reset-view" class="p-2 rounded hover:bg-gray-100">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div id="family-tree"></div>
                </div>
                
                <!-- 统计信息 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="stat-card">
                        <div class="flex items-center">
                            <i class="fas fa-users text-3xl opacity-80 mr-4"></i>
                            <div>
                                <p class="text-sm opacity-90">家族成员</p>
                                <p class="text-3xl font-bold" id="total-members">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card bg-gradient-to-r from-green-500 to-teal-500">
                        <div class="flex items-center">
                            <i class="fas fa-heart text-3xl opacity-80 mr-4"></i>
                            <div>
                                <p class="text-sm opacity-90">在世成员</p>
                                <p class="text-3xl font-bold" id="living-members">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card bg-gradient-to-r from-purple-500 to-pink-500">
                        <div class="flex items-center">
                            <i class="fas fa-camera text-3xl opacity-80 mr-4"></i>
                            <div>
                                <p class="text-sm opacity-90">家族照片</p>
                                <p class="text-3xl font-bold" id="total-photos">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 侧边面板 -->
            <div class="space-y-8">
                <!-- 快速操作 -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bolt mr-2 text-yellow-600"></i>快速操作
                    </h3>
                    <div class="space-y-3">
                        <button id="quick-add-member" class="w-full text-left p-3 rounded-lg hover:bg-blue-50 border border-blue-100">
                            <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                            添加新成员
                        </button>
                        <button id="quick-upload-photo" class="w-full text-left p-3 rounded-lg hover:bg-green-50 border border-green-100">
                            <i class="fas fa-camera text-green-600 mr-2"></i>
                            上传照片
                        </button>
                        <button id="quick-write-blog" class="w-full text-left p-3 rounded-lg hover:bg-purple-50 border border-purple-100">
                            <i class="fas fa-edit text-purple-600 mr-2"></i>
                            写博客
                        </button>
                    </div>
                </div>
                
                <!-- 最近活动 -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-history mr-2 text-blue-600"></i>最近活动
                    </h3>
                    <div id="recent-activities" class="space-y-4">
                        <div class="text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin"></i> 加载中...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- 页脚 -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-xl font-bold flex items-center">
                        <i class="fas fa-tree mr-2"></i>家族树系统
                    </p>
                    <p class="text-gray-400 mt-2">传承家族记忆，连接世代亲情</p>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-gray-400">© 2024 族谱管理系统</p>
                    <p class="text-gray-400 mt-1">技术支持: support@familytree.com</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- 模态框模板 -->
    <div id="modal-container"></div>
    
    <!-- JavaScript -->
    <script src="/assets/js/main.js" type="module"></script>
</body>
</html>
```

#### `frontend/public/assets/css/style.css`

```css
/* 全局样式 */
:root {
    --primary-color: #3b82f6;
    --secondary-color: #10b981;
    --accent-color: #8b5cf6;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --info-color: #06b6d4;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', 'Microsoft YaHei', sans-serif;
    line-height: 1.6;
    color: #374151;
}

/* 卡片样式 */
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

/* 按钮样式 */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    outline: none;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-secondary {
    background: var(--secondary-color);
    color: white;
}

.btn-danger {
    background: var(--danger-color);
    color: white;
}

/* 表单样式 */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #4b5563;
}

.form-input {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

/* 表格样式 */
.table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.table th {
    background: #f9fafb;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.table td {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
}

.table tr:hover {
    background: #f9fafb;
}

/* 响应式设计 */
@media (max-width: 768px) {
    .container {
        padding-left: 16px;
        padding-right: 16px;
    }
    
    .table {
        display: block;
        overflow-x: auto;
    }
}

/* 动画效果 */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeIn 0.5s ease forwards;
}

/* 加载动画 */
.spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #e5e7eb;
    border-top-color: var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* 标签样式 */
.tag {
    display: inline-block;
    padding: 4px 12px;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 20px;
    font-size: 14px;
    margin-right: 8px;
    margin-bottom: 8px;
}

/* 头像样式 */
.avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* 工具提示 */
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltip-text {
    visibility: hidden;
    width: 200px;
    background: #374151;
    color: white;
    text-align: center;
    padding: 8px;
    border-radius: 6px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 14px;
}

.tooltip:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}
```

#### `frontend/public/assets/js/main.js`

```javascript
// 主应用入口
import DataManager from './data-manager.js';
import ChartManager from './chart-manager.js';
import UIManager from './ui-manager.js';
import AuthManager from './auth-manager.js';

class GenealogyApp {
    constructor() {
        this.dataManager = new DataManager();
        this.chartManager = new ChartManager();
        this.uiManager = new UIManager();
        this.authManager = new AuthManager();
        
        this.currentUser = null;
        this.members = [];
        this.photos = [];
        this.blogs = [];
    }
    
    async init() {
        console.log('初始化族谱管理系统...');
        
        // 检查登录状态
        await this.authManager.checkLoginStatus();
        
        // 加载数据
        await this.loadData();
        
        // 初始化UI
        this.uiManager.init(this);
        
        // 绑定事件
        this.bindEvents();
        
        // 更新统计数据
        this.updateStats();
        
        console.log('系统初始化完成');
    }
    
    async loadData() {
        try {
            const token = this.authManager.getToken();
            if (!token) return;
            
            const [members, photos, blogs] = await Promise.all([
                this.dataManager.getMembers(),
                this.dataManager.getRecentPhotos(4),
                this.dataManager.getRecentBlogs(3)
            ]);
            
            this.members = members;
            this.photos = photos;
            this.blogs = blogs;
            
            // 初始化家族树
            this.chartManager.init('family-tree', this.members);
            
            // 更新UI
            this.uiManager.updateRecentActivities(members, photos, blogs);
            
        } catch (error) {
            console.error('加载数据失败:', error);
            this.uiManager.showError('加载数据失败，请检查网络连接');
        }
    }
    
    bindEvents() {
        // 登录按钮
        document.getElementById('login-btn').addEventListener('click', () => {
            this.uiManager.showLoginModal();
        });
        
        // 登出按钮
        document.getElementById('logout-btn').addEventListener('click', () => {
            this.authManager.logout();
            location.reload();
        });
        
        // 快速添加成员
        document.getElementById('quick-add-member').addEventListener('click', () => {
            this.uiManager.showAddMemberModal();
        });
        
        // 快速上传照片
        document.getElementById('quick-upload-photo').addEventListener('click', () => {
            this.uiManager.showPhotoUploadModal();
        });
        
        // 快速写博客
        document.getElementById('quick-write-blog').addEventListener('click', () => {
            this.uiManager.showBlogEditor();
        });
        
        // 图表控制按钮
        document.getElementById('zoom-in').addEventListener('click', () => {
            this.chartManager.zoomIn();
        });
        
        document.getElementById('zoom-out').addEventListener('click', () => {
            this.chartManager.zoomOut();
        });
        
        document.getElementById('reset-view').addEventListener('click', () => {
            this.chartManager.resetView();
        });
    }
    
    updateStats() {
        document.getElementById('total-members').textContent = this.members.length;
        
        const livingMembers = this.members.filter(m => m.is_living);
        document.getElementById('living-members').textContent = livingMembers.length;
        
        document.getElementById('total-photos').textContent = this.photos.length;
    }
    
    // 添加新成员
    async addMember(memberData) {
        try {
            const newMember = await this.dataManager.addMember(memberData);
            this.members.push(newMember);
            
            // 更新图表
            this.chartManager.update(this.members);
            
            // 更新统计
            this.updateStats();
            
            return newMember;
        } catch (error) {
            console.error('添加成员失败:', error);
            throw error;
        }
    }
    
    // 更新成员信息
    async updateMember(id, memberData) {
        try {
            const updatedMember = await this.dataManager.updateMember(id, memberData);
            const index = this.members.findIndex(m => m.id === id);
            if (index !== -1) {
                this.members[index] = updatedMember;
                this.chartManager.update(this.members);
            }
            return updatedMember;
        } catch (error) {
            console.error('更新成员失败:', error);
            throw error;
        }
    }
    
    // 删除成员
    async deleteMember(id) {
        try {
            await this.dataManager.deleteMember(id);
            this.members = this.members.filter(m => m.id !== id);
            this.chartManager.update(this.members);
            this.updateStats();
        } catch (error) {
            console.error('删除成员失败:', error);
            throw error;
        }
    }
    
    // 上传照片
    async uploadPhoto(file, photoData) {
        try {
            const newPhoto = await this.dataManager.uploadPhoto(file, photoData);
            this.photos.unshift(newPhoto);
            return newPhoto;
        } catch (error) {
            console.error('上传照片失败:', error);
            throw error;
        }
    }
    
    // 发布博客
    async createBlog(blogData) {
        try {
            const newBlog = await this.dataManager.createBlog(blogData);
            this.blogs.unshift(newBlog);
            return newBlog;
        } catch (error) {
            console.error('发布博客失败:', error);
            throw error;
        }
    }
}

// 初始化应用
window.addEventListener('DOMContentLoaded', async () => {
    const app = new GenealogyApp();
    window.app = app; // 全局访问
    await app.init();
});
```

#### `frontend/public/assets/js/data-manager.js`

```javascript
// 数据管理类
class DataManager {
    constructor() {
        this.baseURL = 'http://localhost:3000/api';
        this.cache = new Map();
        this.cacheDuration = 5 * 60 * 1000; // 5分钟缓存
    }
    
    async request(endpoint, options = {}) {
        const token = localStorage.getItem('auth_token');
        const headers = {
            'Content-Type': 'application/json',
            ...options.headers
        };
        
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            ...options,
            headers
        });
        
        if (!response.ok) {
            const error = await response.json().catch(() => ({}));
            throw new Error(error.message || `请求失败: ${response.status}`);
        }
        
        return response.json();
    }
    
    // 成员相关操作
    async getMembers() {
        const cacheKey = 'members';
        const cached = this.getFromCache(cacheKey);
        
        if (cached) {
            return cached;
        }
        
        const data = await this.request('/members');
        this.setToCache(cacheKey, data);
        return data;
    }
    
    async getMember(id) {
        return await this.request(`/members/${id}`);
    }
    
    async addMember(memberData) {
        const newMember = await this.request('/members', {
            method: 'POST',
            body: JSON.stringify(memberData)
        });
        
        this.clearCache('members');
        return newMember;
    }
    
    async updateMember(id, memberData) {
        const updatedMember = await this.request(`/members/${id}`, {
            method: 'PUT',
            body: JSON.stringify(memberData)
        });
        
        this.clearCache('members');
        return updatedMember;
    }
    
    async deleteMember(id) {
        await this.request(`/members/${id}`, {
            method: 'DELETE'
        });
        
        this.clearCache('members');
    }
    
    // 照片相关操作
    async getPhotos(params = {}) {
        const query = new URLSearchParams(params).toString();
        return await this.request(`/photos?${query}`);
    }
    
    async getRecentPhotos(limit = 4) {
        return await this.getPhotos({ limit, order: 'desc' });
    }
    
    async uploadPhoto(file, photoData) {
        const formData = new FormData();
        formData.append('photo', file);
        formData.append('title', photoData.title || '');
        formData.append('description', photoData.description || '');
        formData.append('tags', JSON.stringify(photoData.tags || []));
        
        // 临时移除Content-Type头，让浏览器自动设置
        const response = await fetch(`${this.baseURL}/photos/upload`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
            },
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('上传照片失败');
        }
        
        return response.json();
    }
    
    async deletePhoto(id) {
        await this.request(`/photos/${id}`, {
            method: 'DELETE'
        });
    }
    
    // 博客相关操作
    async getBlogs(params = {}) {
        const query = new URLSearchParams(params).toString();
        return await this.request(`/blogs?${query}`);
    }
    
    async getRecentBlogs(limit = 3) {
        return await this.getBlogs({ limit, order: 'desc' });
    }
    
    async getBlog(id) {
        return await this.request(`/blogs/${id}`);
    }
    
    async createBlog(blogData) {
        return await this.request('/blogs', {
            method: 'POST',
            body: JSON.stringify(blogData)
        });
    }
    
    async updateBlog(id, blogData) {
        return await this.request(`/blogs/${id}`, {
            method: 'PUT',
            body: JSON.stringify(blogData)
        });
    }
    
    async deleteBlog(id) {
        await this.request(`/blogs/${id}`, {
            method: 'DELETE'
        });
    }
    
    // 评论相关操作
    async addComment(blogId, content) {
        return await this.request(`/blogs/${blogId}/comments`, {
            method: 'POST',
            body: JSON.stringify({ content })
        });
    }
    
    // 缓存管理
    getFromCache(key) {
        const cached = this.cache.get(key);
        if (!cached) return null;
        
        if (Date.now() - cached.timestamp > this.cacheDuration) {
            this.cache.delete(key);
            return null;
        }
        
        return cached.data;
    }
    
    setToCache(key, data) {
        this.cache.set(key, {
            data,
            timestamp: Date.now()
        });
    }
    
    clearCache(key) {
        if (key) {
            this.cache.delete(key);
        } else {
            this.cache.clear();
        }
    }
}

export default DataManager;
```

#### `frontend/public/assets/js/chart-manager.js`

```javascript
// 图表管理类
class ChartManager {
    constructor() {
        this.chart = null;
        this.config = {
            tooltip: {
                trigger: 'item',
                triggerOn: 'mousemove',
                formatter: function(params) {
                    return `
                        <div style="padding: 10px; background: white; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.15)">
                            <div style="font-weight: bold; color: #333; margin-bottom: 5px;">
                                ${params.name}
                            </div>
                            <div style="color: #666; font-size: 12px;">
                                ${params.data.desc || '家族成员'}
                            </div>
                        </div>
                    `;
                }
            },
            series: [{
                type: 'tree',
                data: [],
                top: '10%',
                left: '8%',
                bottom: '22%',
                right: '20%',
                symbolSize: 10,
                label: {
                    position: 'left',
                    verticalAlign: 'middle',
                    align: 'right',
                    fontSize: 14,
                    color: '#333'
                },
                leaves: {
                    label: {
                        position: 'right',
                        verticalAlign: 'middle',
                        align: 'left'
                    }
                },
                lineStyle: {
                    color: '#c9d4d6',
                    width: 2,
                    curveness: 0.3
                },
                expandAndCollapse: true,
                animationDuration: 550,
                animationDurationUpdate: 750
            }]
        };
    }
    
    init(domId, members) {
        if (!echarts) {
            console.error('ECharts未加载');
            return;
        }
        
        const dom = document.getElementById(domId);
        if (!dom) {
            console.error(`找不到DOM元素: #${domId}`);
            return;
        }
        
        this.chart = echarts.init(dom);
        this.update(members);
        
        // 添加点击事件
        this.chart.on('click', (params) => {
            if (params.data && params.data.id) {
                this.showMemberDetail(params.data.id);
            }
        });
        
        // 响应窗口大小变化
        window.addEventListener('resize', () => {
            this.chart.resize();
        });
    }
    
    update(members) {
        if (!this.chart) return;
        
        const treeData = this.buildTreeData(members);
        this.config.series[0].data = [treeData];
        this.chart.setOption(this.config);
    }
    
    buildTreeData(members) {
        if (!members || members.length === 0) {
            return {
                name: '暂无数据',
                itemStyle: { color: '#ddd' }
            };
        }
        
        // 找到根节点（没有父母的节点）
        const rootMember = members.find(member => 
            !member.parents || member.parents.length === 0
        ) || members[0];
        
        const buildNode = (member) => {
            const children = members.filter(m => 
                m.parents && m.parents.includes(member.id)
            );
            
            const node = {
                name: member.name,
                id: member.id,
                desc: this.getMemberDescription(member),
                value: member.id,
                children: children.map(buildNode),
                itemStyle: {
                    color: member.gender === 'male' ? '#3b82f6' : '#ec4899'
                }
            };
            
            return node;
        };
        
        return buildNode(rootMember);
    }
    
    getMemberDescription(member) {
        const parts = [];
        
        if (member.birth_date) {
            parts.push(`出生: ${member.birth_date}`);
        }
        
        if (member.occupation) {
            parts.push(`职业: ${member.occupation}`);
        }
        
        if (member.description) {
            parts.push(`简介: ${member.description.substring(0, 50)}...`);
        }
        
        return parts.join('<br>');
    }
    
    zoomIn() {
        if (!this.chart) return;
        const currentZoom = this.chart.getOption().series[0].zoom || 1;
        this.chart.setOption({
            series: [{
                zoom: currentZoom * 1.2
            }]
        });
    }
    
    zoomOut() {
        if (!this.chart) return;
        const currentZoom = this.chart.getOption().series[0].zoom || 1;
        this.chart.setOption({
            series: [{
                zoom: currentZoom * 0.8
            }]
        });
    }
    
    resetView() {
        if (!this.chart) return;
        this.chart.setOption({
            series: [{
                zoom: 1,
                left: '8%',
                right: '20%'
            }]
        });
    }
    
    async showMemberDetail(memberId) {
        if (!window.app) return;
        
        try {
            const member = await window.app.dataManager.getMember(memberId);
            window.app.uiManager.showMemberDetailModal(member);
        } catch (error) {
            console.error('获取成员详情失败:', error);
        }
    }
}

export default ChartManager;
```

#### `frontend/public/assets/js/ui-manager.js`

```javascript
// UI管理类
class UIManager {
    constructor() {
        this.modals = new Map();
        this.currentMember = null;
    }
    
    init(app) {
        this.app = app;
        this.initModals();
        this.updateUserInfo();
    }
    
    initModals() {
        // 创建模态框容器
        const modalContainer = document.getElementById('modal-container');
        if (!modalContainer) {
            console.warn('找不到模态框容器');
            return;
        }
        
        // 登录模态框
        modalContainer.innerHTML += `
            <div id="login-modal" class="modal-overlay hidden">
                <div class="modal-content max-w-md">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">用户登录</h3>
                        <button class="close-modal text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                    
                    <form id="login-form">
                        <div class="form-group">
                            <label class="form-label">用户名</label>
                            <input type="text" id="login-username" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">密码</label>
                            <input type="password" id="login-password" class="form-input" required>
                        </div>
                        
                        <div class="flex justify-between items-center mt-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt mr-2"></i>登录
                            </button>
                            <button type="button" class="text-blue-600 hover:text-blue-800">
                                注册账号
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        
        // 绑定模态框事件
        this.bindModalEvents();
    }
    
    bindModalEvents() {
        // 关闭模态框
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay') || 
                e.target.classList.contains('close-modal') ||
                e.target.closest('.close-modal')) {
                this.hideModal(e.target.closest('.modal-overlay'));
            }
        });
        
        // 登录表单提交
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleLogin();
            });
        }
    }
    
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    hideModal(modal) {
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }
    
    showLoginModal() {
        this.showModal('login-modal');
    }
    
    showAddMemberModal() {
        this.showMemberModal({ mode: 'add' });
    }
    
    showEditMemberModal(member) {
        this.showMemberModal({ mode: 'edit', member });
    }
    
    showMemberDetailModal(member) {
        this.showMemberModal({ mode: 'view', member });
    }
    
    async showMemberModal(options) {
        const { mode, member } = options;
        const title = mode === 'add' ? '添加成员' : 
                     mode === 'edit' ? '编辑成员' : '成员详情';
        
        const modalHtml = `
            <div id="member-modal" class="modal-overlay">
                <div class="modal-content max-w-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">${title}</h3>
                        <button class="close-modal text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                    
                    <form id="member-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">姓名 *</label>
                                <input type="text" name="name" class="form-input" 
                                       value="${member?.name || ''}" ${mode === 'view' ? 'readonly' : 'required'}>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">性别</label>
                                <select name="gender" class="form-input" ${mode === 'view' ? 'disabled' : ''}>
                                    <option value="male" ${member?.gender === 'male' ? 'selected' : ''}>男</option>
                                    <option value="female" ${member?.gender === 'female' ? 'selected' : ''}>女</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">出生日期</label>
                                <input type="date" name="birth_date" class="form-input" 
                                       value="${member?.birth_date || ''}" ${mode === 'view' ? 'readonly' : ''}>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">手机号</label>
                                <input type="tel" name="phone" class="form-input" 
                                       value="${member?.phone || ''}" ${mode === 'view' ? 'readonly' : ''}>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="form-label">邮箱</label>
                                <input type="email" name="email" class="form-input" 
                                       value="${member?.email || ''}" ${mode === 'view' ? 'readonly' : ''}>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="form-label">职业</label>
                                <input type="text" name="occupation" class="form-input" 
                                       value="${member?.occupation || ''}" ${mode === 'view' ? 'readonly' : ''}>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="form-label">个人简介</label>
                                <textarea name="description" rows="3" class="form-input" 
                                          ${mode === 'view' ? 'readonly' : ''}>${member?.description || ''}</textarea>
                            </div>
                        </div>
                        
                        ${mode !== 'view' ? `
                            <div class="flex justify-end mt-6 space-x-3">
                                <button type="button" class="close-modal btn">
                                    取消
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    ${mode === 'add' ? '添加成员' : '保存修改'}
                                </button>
                            </div>
                        ` : ''}
                    </form>
                </div>
            </div>
        `;
        
        // 添加模态框到页面
        const modalContainer = document.getElementById('modal-container');
        modalContainer.innerHTML = modalHtml;
        
        // 绑定表单提交事件
        if (mode !== 'view') {
            const form = document.getElementById('member-form');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleMemberFormSubmit(mode, member?.id);
            });
        }
    }
    
    async handleLogin() {
        const username = document.getElementById('login-username').value;
        const password = document.getElementById('login-password').value;
        
        try {
            await this.app.authManager.login(username, password);
            this.hideModal(document.getElementById('login-modal'));
            this.updateUserInfo();
            location.reload();
        } catch (error) {
            this.showError('登录失败: ' + error.message);
        }
    }
    
    async handleMemberFormSubmit(mode, memberId) {
        const form = document.getElementById('member-form');
        const formData = new FormData(form);
        const memberData = Object.fromEntries(formData.entries());
        
        try {
            if (mode === 'add') {
                await this.app.addMember(memberData);
                this.showSuccess('成员添加成功');
            } else if (mode === 'edit') {
                await this.app.updateMember(memberId, memberData);
                this.showSuccess('成员信息更新成功');
            }
            
            this.hideModal(document.getElementById('member-modal'));
        } catch (error) {
            this.showError('操作失败: ' + error.message);
        }
    }
    
    updateUserInfo() {
        const userInfo = document.getElementById('user-info');
        const loginBtn = document.getElementById('login-btn');
        const usernameSpan = document.getElementById('username');
        
        const token = localStorage.getItem('auth_token');
        const user = JSON.parse(localStorage.getItem('user_info') || 'null');
        
        if (token && user) {
            userInfo.classList.remove('hidden');
            loginBtn.classList.add('hidden');
            usernameSpan.textContent = user.username;
        } else {
            userInfo.classList.add('hidden');
            loginBtn.classList.remove('hidden');
        }
    }
    
    updateRecentActivities(members, photos, blogs) {
        const container = document.getElementById('recent-activities');
        if (!container) return;
        
        const activities = [];
        
        // 添加最近添加的成员
        const recentMembers = members.slice(0, 3);
        recentMembers.forEach(member => {
            activities.push({
                type: 'member',
                icon: 'user-plus',
                color: 'blue',
                text: `添加了新成员: ${member.name}`,
                time: '刚刚'
            });
        });
        
        // 添加最近上传的照片
        const recentPhotos = photos.slice(0, 2);
        recentPhotos.forEach(photo => {
            activities.push({
                type: 'photo',
                icon: 'camera',
                color: 'green',
                text: `上传了新照片: ${photo.title || '未命名'}`,
                time: '1小时前'
            });
        });
        
        // 添加最近的博客
        const recentBlogs = blogs.slice(0, 2);
        recentBlogs.forEach(blog => {
            activities.push({
                type: 'blog',
                icon: 'edit',
                color: 'purple',
                text: `发布了新博客: ${blog.title}`,
                time: '2小时前'
            });
        });
        
        // 按时间排序（这里简化为倒序）
        activities.sort(() => -1);
        
        // 更新HTML
        container.innerHTML = activities.map(activity => `
            <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-${activity.color}-100 flex items-center justify-center">
                        <i class="fas fa-${activity.icon} text-${activity.color}-600"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900">${activity.text}</p>
                    <p class="text-xs text-gray-500">${activity.time}</p>
                </div>
            </div>
        `).join('');
    }
    
    showError(message) {
        this.showNotification(message, 'error');
    }
    
    showSuccess(message) {
        this.showNotification(message, 'success');
    }
    
    showInfo(message) {
        this.showNotification(message, 'info');
    }
    
    showNotification(message, type = 'info') {
        const colors = {
            error: 'red',
            success: 'green',
            info: 'blue',
            warning: 'yellow'
        };
        
        const color = colors[type] || 'blue';
        
        // 创建通知元素
        const notification = document.createElement('div');
        notification.className = `
            fixed top-4 right-4 z-50 
            bg-${color}-100 border border-${color}-400 text-${color}-700 
            px-4 py-3 rounded-lg shadow-lg 
            animate-fadeIn
        `;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} mr-2"></i>
                <span>${message}</span>
                <button class="ml-4 text-${color}-700 hover:text-${color}-900" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // 添加到页面
        document.body.appendChild(notification);
        
        // 5秒后自动移除
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

export default UIManager;
```

#### `frontend/public/assets/js/auth-manager.js`

```javascript
// 认证管理类
class AuthManager {
    constructor() {
        this.baseURL = 'http://localhost:3000/api';
        this.tokenKey = 'auth_token';
        this.userKey = 'user_info';
    }
    
    async checkLoginStatus() {
        const token = this.getToken();
        if (!token) return false;
        
        try {
            // 验证令牌是否有效
            const response = await fetch(`${this.baseURL}/auth/verify`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            
            if (!response.ok) {
                this.clearAuth();
                return false;
            }
            
            return true;
        } catch (error) {
            console.error('验证登录状态失败:', error);
            this.clearAuth();
            return false;
        }
    }
    
    async login(username, password) {
        try {
            const response = await fetch(`${this.baseURL}/auth/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });
            
            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || '登录失败');
            }
            
            const data = await response.json();
            
            // 保存令牌和用户信息
            this.saveAuth(data.token, data.user);
            
            return data.user;
        } catch (error) {
            console.error('登录失败:', error);
            throw error;
        }
    }
    
    async register(userData) {
        try {
            const response = await fetch(`${this.baseURL}/auth/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(userData)
            });
            
            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || '注册失败');
            }
            
            return await response.json();
        } catch (error) {
            console.error('注册失败:', error);
            throw error;
        }
    }
    
    logout() {
        this.clearAuth();
        window.location.href = '/';
    }
    
    saveAuth(token, user) {
        localStorage.setItem(this.tokenKey, token);
        localStorage.setItem(this.userKey, JSON.stringify(user));
    }
    
    clearAuth() {
        localStorage.removeItem(this.tokenKey);
        localStorage.removeItem(this.userKey);
    }
    
    getToken() {
        return localStorage.getItem(this.tokenKey);
    }
    
    getUser() {
        const userStr = localStorage.getItem(this.userKey);
        return userStr ? JSON.parse(userStr) : null;
    }
    
    isAuthenticated() {
        return !!this.getToken();
    }
    
    isAdmin() {
        const user = this.getUser();
        return user && user.role === 'admin';
    }
    
    isEditor() {
        const user = this.getUser();
        return user && (user.role === 'admin' || user.role === 'editor');
    }
    
    // 请求拦截器
    async authorizedRequest(url, options = {}) {
        const token = this.getToken();
        
        if (!token) {
            throw new Error('用户未登录');
        }
        
        const headers = {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            ...options.headers
        };
        
        const response = await fetch(url, {
            ...options,
            headers
        });
        
        if (response.status === 401) {
            // 令牌过期，清除认证信息
            this.clearAuth();
            window.location.href = '/?login=true';
            throw new Error('登录已过期，请重新登录');
        }
        
        return response;
    }
}

export default AuthManager;
```

### 2. 后端文件

#### `backend/package.json`

```json
{
  "name": "family-tree-backend",
  "version": "1.0.0",
  "description": "族谱管理系统后端API",
  "main": "src/server.js",
  "scripts": {
    "start": "node src/server.js",
    "dev": "nodemon src/server.js",
    "test": "jest",
    "migrate": "node src/database/migrate.js"
  },
  "dependencies": {
    "express": "^4.18.2",
    "sqlite3": "^5.1.6",
    "bcryptjs": "^2.4.3",
    "jsonwebtoken": "^9.0.0",
    "multer": "^1.4.5-lts.1",
    "cors": "^2.8.5",
    "dotenv": "^16.3.1",
    "express-validator": "^7.0.1",
    "helmet": "^7.0.0",
    "compression": "^1.7.4",
    "winston": "^3.10.0",
    "express-rate-limit": "^6.10.0"
  },
  "devDependencies": {
    "nodemon": "^2.0.22",
    "jest": "^29.6.1",
    "supertest": "^6.3.3"
  },
  "keywords": [
    "family-tree",
    "genealogy",
    "api"
  ],
  "author": "Your Name",
  "license": "MIT"
}
```

#### `backend/src/server.js`

```javascript
const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const compression = require('compression');
const rateLimit = require('express-rate-limit');
const path = require('path');
require('dotenv').config();

// 导入路由
const authRoutes = require('./routes/auth');
const memberRoutes = require('./routes/members');
const photoRoutes = require('./routes/photos');
const blogRoutes = require('./routes/blogs');

// 导入中间件
const { errorHandler, notFound } = require('./middleware/error');

const app = express();
const PORT = process.env.PORT || 3000;

// 安全中间件
app.use(helmet());
app.use(compression());

// CORS配置
app.use(cors({
    origin: process.env.CLIENT_URL || 'http://localhost:8080',
    credentials: true
}));

// 请求限制
const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15分钟
    max: 100, // 每个IP限制100个请求
    message: '请求过多，请稍后再试'
});
app.use('/api/', limiter);

// 解析请求体
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true, limit: '10mb' }));

// 静态文件服务
app.use(express.static(path.join(__dirname, '../../frontend/public')));

// API路由
app.use('/api/auth', authRoutes);
app.use('/api/members', memberRoutes);
app.use('/api/photos', photoRoutes);
app.use('/api/blogs', blogRoutes);

// 健康检查
app.get('/health', (req, res) => {
    res.json({ status: 'OK', timestamp: new Date().toISOString() });
});

// 404处理
app.use(notFound);

// 错误处理
app.use(errorHandler);

// 启动服务器
const server = app.listen(PORT, () => {
    console.log(`🚀 服务器运行在 http://localhost:${PORT}`);
    console.log(`📁 API文档: http://localhost:${PORT}/api/docs`);
});

// 优雅关闭
process.on('SIGTERM', () => {
    console.log('🛑 收到SIGTERM信号，正在关闭服务器...');
    server.close(() => {
        console.log('✅ 服务器已关闭');
        process.exit(0);
    });
});

module.exports = app;
```

#### `backend/src/database.js`

```javascript
const sqlite3 = require('sqlite3').verbose();
const path = require('path');
const bcrypt = require('bcryptjs');

class Database {
    constructor() {
        this.dbPath = path.join(__dirname, '../database/family.db');
        this.db = null;
    }
    
    async connect() {
        return new Promise((resolve, reject) => {
            this.db = new sqlite3.Database(this.dbPath, (err) => {
                if (err) {
                    console.error('❌ 连接数据库失败:', err);
                    reject(err);
                } else {
                    console.log('✅ 已连接到SQLite数据库');
                    this.initialize()
                        .then(resolve)
                        .catch(reject);
                }
            });
        });
    }
    
    async initialize() {
        await this.createTables();
        await this.createIndexes();
        await this.seedData();
    }
    
    async createTables() {
        const queries = [
            // 成员表
            `CREATE TABLE IF NOT EXISTS members (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                gender TEXT CHECK(gender IN ('male', 'female')),
                birth_date DATE,
                death_date DATE,
                avatar_url TEXT,
                phone TEXT,
                wechat TEXT,
                tiktok TEXT,
                email TEXT,
                address TEXT,
                occupation TEXT,
                description TEXT,
                is_living BOOLEAN DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )`,
            
            // 关系表
            `CREATE TABLE IF NOT EXISTS relationships (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                member1_id INTEGER NOT NULL,
                member2_id INTEGER NOT NULL,
                relationship_type TEXT CHECK(relationship_type IN ('parent', 'spouse', 'sibling')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (member1_id) REFERENCES members(id) ON DELETE CASCADE,
                FOREIGN KEY (member2_id) REFERENCES members(id) ON DELETE CASCADE
            )`,
            
            // 照片表
            `CREATE TABLE IF NOT EXISTS photos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER,
                url TEXT NOT NULL,
                title TEXT,
                description TEXT,
                uploader_id INTEGER,
                upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                tags TEXT,
                is_private BOOLEAN DEFAULT 0,
                FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
            )`,
            
            // 博客表
            `CREATE TABLE IF NOT EXISTS blogs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                content TEXT NOT NULL,
                summary TEXT,
                publish_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                view_count INTEGER DEFAULT 0,
                likes INTEGER DEFAULT 0,
                is_published BOOLEAN DEFAULT 1,
                FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
            )`,
            
            // 用户表
            `CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER UNIQUE,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                role TEXT CHECK(role IN ('admin', 'editor', 'viewer')) DEFAULT 'viewer',
                is_active BOOLEAN DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP,
                FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL
            )`,
            
            // 评论表
            `CREATE TABLE IF NOT EXISTS comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                blog_id INTEGER NOT NULL,
                member_id INTEGER NOT NULL,
                content TEXT NOT NULL,
                parent_comment_id INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_approved BOOLEAN DEFAULT 1,
                FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
                FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
                FOREIGN KEY (parent_comment_id) REFERENCES comments(id) ON DELETE CASCADE
            )`
        ];
        
        for (const query of queries) {
            await this.run(query);
        }
    }
    
    async createIndexes() {
        const queries = [
            'CREATE INDEX IF NOT EXISTS idx_members_name ON members(name)',
            'CREATE INDEX IF NOT EXISTS idx_members_birth_date ON members(birth_date)',
            'CREATE INDEX IF NOT EXISTS idx_photos_upload_date ON photos(upload_date)',
            'CREATE INDEX IF NOT EXISTS idx_blogs_publish_date ON blogs(publish_date)',
            'CREATE INDEX IF NOT EXISTS idx_users_username ON users(username)',
            'CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)'
        ];
        
        for (const query of queries) {
            await this.run(query);
        }
    }
    
    async seedData() {
        // 检查是否已有数据
        const count = await this.get('SELECT COUNT(*) as count FROM members');
        if (count.count > 0) return;
        
        console.log('🌱 正在初始化数据库数据...');
        
        // 创建默认管理员用户
        const passwordHash = await bcrypt.hash('admin123', 10);
        await this.run(
            'INSERT INTO users (username, password_hash, email, role) VALUES (?, ?, ?, ?)',
            ['admin', passwordHash, 'admin@familytree.com', 'admin']
        );
        
        // 创建示例成员数据
        await this.run(
            `INSERT INTO members (name, gender, birth_date, occupation, description) 
             VALUES (?, ?, ?, ?, ?)`,
            ['张氏祖先', 'male', '1900-01-01', '家族创始人', '家族的第一代祖先']
        );
        
        console.log('✅ 数据库初始化完成');
    }
    
    // 通用数据库操作方法
    run(sql, params = []) {
        return new Promise((resolve, reject) => {
            this.db.run(sql, params, function(err) {
                if (err) {
                    reject(err);
                } else {
                    resolve({ id: this.lastID, changes: this.changes });
                }
            });
        });
    }
    
    get(sql, params = []) {
        return new Promise((resolve, reject) => {
            this.db.get(sql, params, (err, result) => {
                if (err) {
                    reject(err);
                } else {
                    resolve(result);
                }
            });
        });
    }
    
    all(sql, params = []) {
        return new Promise((resolve, reject) => {
            this.db.all(sql, params, (err, rows) => {
                if (err) {
                    reject(err);
                } else {
                    resolve(rows);
                }
            });
        });
    }
    
    // 成员相关方法
    async getAllMembers() {
        return this.all('SELECT * FROM members ORDER BY created_at DESC');
    }
    
    async getMemberById(id) {
        return this.get('SELECT * FROM members WHERE id = ?', [id]);
    }
    
    async createMember(memberData) {
        const { name, gender, birth_date, phone, email, occupation, description } = memberData;
        const result = await this.run(
            `INSERT INTO members (name, gender, birth_date, phone, email, occupation, description) 
             VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [name, gender, birth_date, phone, email, occupation, description]
        );
        
        return { id: result.id, ...memberData };
    }
    
    async updateMember(id, memberData) {
        const fields = [];
        const values = [];
        
        for (const [key, value] of Object.entries(memberData)) {
            if (value !== undefined) {
                fields.push(`${key} = ?`);
                values.push(value);
            }
        }
        
        if (fields.length === 0) {
            throw new Error('没有要更新的字段');
        }
        
        fields.push('updated_at = CURRENT_TIMESTAMP');
        values.push(id);
        
        await this.run(
            `UPDATE members SET ${fields.join(', ')} WHERE id = ?`,
            values
        );
        
        return this.getMemberById(id);
    }
    
    async deleteMember(id) {
        await this.run('DELETE FROM members WHERE id = ?', [id]);
        return { success: true };
    }
    
    // 用户相关方法
    async getUserByUsername(username) {
        return this.get('SELECT * FROM users WHERE username = ?', [username]);
    }
    
    async createUser(userData) {
        const { username, password_hash, email, role, member_id } = userData;
        const result = await this.run(
            `INSERT INTO users (username, password_hash, email, role, member_id) 
             VALUES (?, ?, ?, ?, ?)`,
            [username, password_hash, email, role || 'viewer', member_id]
        );
        
        return { id: result.id, ...userData };
    }
    
    async updateUserLogin(id) {
        await this.run(
            'UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?',
            [id]
        );
    }
    
    // 照片相关方法
    async getPhotos(params = {}) {
        const { limit = 10, offset = 0, order = 'desc' } = params;
        return this.all(
            `SELECT * FROM photos 
             ORDER BY upload_date ${order.toUpperCase()} 
             LIMIT ? OFFSET ?`,
            [limit, offset]
        );
    }
    
    async createPhoto(photoData) {
        const { url, title, description, uploader_id, tags, member_id } = photoData;
        const result = await this.run(
            `INSERT INTO photos (url, title, description, uploader_id, tags, member_id) 
             VALUES (?, ?, ?, ?, ?, ?)`,
            [url, title, description, uploader_id, JSON.stringify(tags || []), member_id]
        );
        
        return { id: result.id, ...photoData };
    }
    
    // 博客相关方法
    async getBlogs(params = {}) {
        const { limit = 10, offset = 0, order = 'desc' } = params;
        return this.all(
            `SELECT b.*, m.name as author_name 
             FROM blogs b 
             LEFT JOIN members m ON b.member_id = m.id
             WHERE b.is_published = 1
             ORDER BY publish_date ${order.toUpperCase()} 
             LIMIT ? OFFSET ?`,
            [limit, offset]
        );
    }
    
    async createBlog(blogData) {
        const { title, content, member_id, summary } = blogData;
        const result = await this.run(
            `INSERT INTO blogs (title, content, member_id, summary) 
             VALUES (?, ?, ?, ?)`,
            [title, content, member_id, summary || content.substring(0, 200)]
        );
        
        return { id: result.id, ...blogData };
    }
    
    // 关闭数据库连接
    close() {
        if (this.db) {
            this.db.close();
        }
    }
}

// 创建单例实例
const db = new Database();

// 导出数据库实例
module.exports = db;
```

#### `backend/src/routes/members.js`

```javascript
const express = require('express');
const router = express.Router();
const db = require('../database');
const { authenticate, isEditor } = require('../middleware/auth');
const { validateMember } = require('../middleware/validation');

// 获取所有成员
router.get('/', authenticate, async (req, res) => {
    try {
        const members = await db.getAllMembers();
        res.json(members);
    } catch (error) {
        console.error('获取成员列表失败:', error);
        res.status(500).json({ error: '获取成员列表失败' });
    }
});

// 获取单个成员
router.get('/:id', authenticate, async (req, res) => {
    try {
        const member = await db.getMemberById(req.params.id);
        if (!member) {
            return res.status(404).json({ error: '成员不存在' });
        }
        res.json(member);
    } catch (error) {
        console.error('获取成员失败:', error);
        res.status(500).json({ error: '获取成员失败' });
    }
});

// 创建新成员
router.post('/', authenticate, isEditor, validateMember, async (req, res) => {
    try {
        const member = await db.createMember(req.body);
        res.status(201).json(member);
    } catch (error) {
        console.error('创建成员失败:', error);
        res.status(500).json({ error: '创建成员失败' });
    }
});

// 更新成员
router.put('/:id', authenticate, isEditor, validateMember, async (req, res) => {
    try {
        const member = await db.updateMember(req.params.id, req.body);
        res.json(member);
    } catch (error) {
        console.error('更新成员失败:', error);
        res.status(500).json({ error: '更新成员失败' });
    }
});

// 删除成员
router.delete('/:id', authenticate, isEditor, async (req, res) => {
    try {
        await db.deleteMember(req.params.id);
        res.json({ success: true, message: '成员删除成功' });
    } catch (error) {
        console.error('删除成员失败:', error);
        res.status(500).json({ error: '删除成员失败' });
    }
});

// 搜索成员
router.get('/search/:keyword', authenticate, async (req, res) => {
    try {
        const members = await db.all(
            `SELECT * FROM members 
             WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? 
             ORDER BY name`,
            [`%${req.params.keyword}%`, `%${req.params.keyword}%`, `%${req.params.keyword}%`]
        );
        res.json(members);
    } catch (error) {
        console.error('搜索成员失败:', error);
        res.status(500).json({ error: '搜索成员失败' });
    }
});

// 获取家族树数据
router.get('/tree/data', authenticate, async (req, res) => {
    try {
        const members = await db.getAllMembers();
        
        // 构建树形结构
        const treeData = buildFamilyTree(members);
        
        res.json(treeData);
    } catch (error) {
        console.error('获取家族树数据失败:', error);
        res.status(500).json({ error: '获取家族树数据失败' });
    }
});

function buildFamilyTree(members) {
    // 找到根节点（没有父母的节点）
    const rootMember = members.find(member => {
        // 这里需要根据实际情况判断根节点
        // 暂时返回第一个成员作为根节点
        return true;
    });
    
    if (!rootMember) {
        return null;
    }
    
    const buildNode = (member) => {
        // 获取子节点
        // 这里需要根据关系表查询子节点
        // 暂时返回空子节点
        return {
            id: member.id,
            name: member.name,
            gender: member.gender,
            birthDate: member.birth_date,
            children: []
        };
    };
    
    return buildNode(rootMember);
}

module.exports = router;
```

#### `backend/src/middleware/auth.js`

```javascript
const jwt = require('jsonwebtoken');
const db = require('../database');

const JWT_SECRET = process.env.JWT_SECRET || 'your-secret-key';

// 认证中间件
const authenticate = async (req, res, next) => {
    try {
        const authHeader = req.headers.authorization;
        
        if (!authHeader || !authHeader.startsWith('Bearer ')) {
            return res.status(401).json({ error: '未提供认证令牌' });
        }
        
        const token = authHeader.split(' ')[1];
        
        // 验证令牌
        const decoded = jwt.verify(token, JWT_SECRET);
        
        // 获取用户信息
        const user = await db.get('SELECT * FROM users WHERE id = ?', [decoded.id]);
        
        if (!user || !user.is_active) {
            return res.status(401).json({ error: '用户不存在或已被禁用' });
        }
        
        // 将用户信息添加到请求对象
        req.user = user;
        req.userId = user.id;
        req.userRole = user.role;
        
        next();
    } catch (error) {
        if (error.name === 'TokenExpiredError') {
            return res.status(401).json({ error: '令牌已过期' });
        }
        
        if (error.name === 'JsonWebTokenError') {
            return res.status(401).json({ error: '无效的令牌' });
        }
        
        console.error('认证失败:', error);
        res.status(500).json({ error: '认证失败' });
    }
};

// 检查是否为管理员
const isAdmin = (req, res, next) => {
    if (req.userRole !== 'admin') {
        return res.status(403).json({ error: '需要管理员权限' });
    }
    next();
};

// 检查是否为编辑者
const isEditor = (req, res, next) => {
    const allowedRoles = ['admin', 'editor'];
    if (!allowedRoles.includes(req.userRole)) {
        return res.status(403).json({ error: '权限不足' });
    }
    next();
};

// 生成JWT令牌
const generateToken = (user) => {
    return jwt.sign(
        { 
            id: user.id, 
            username: user.username,
            role: user.role,
            member_id: user.member_id
        },
        JWT_SECRET,
        { expiresIn: '7d' }
    );
};

// 验证令牌
const verifyToken = (token) => {
    try {
        return jwt.verify(token, JWT_SECRET);
    } catch (error) {
        return null;
    }
};

module.exports = {
    authenticate,
    isAdmin,
    isEditor,
    generateToken,
    verifyToken
};
```

### 3. 配置文件

#### `.env.example`

```env
# 服务器配置
PORT=3000
NODE_ENV=development
CLIENT_URL=http://localhost:8080

# 数据库配置
DB_PATH=./database/family.db

# JWT配置
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
JWT_EXPIRES_IN=7d

# 文件上传配置
UPLOAD_DIR=./uploads
MAX_FILE_SIZE=10mb
ALLOWED_FILE_TYPES=image/jpeg,image/png,image/gif

# 安全配置
RATE_LIMIT_WINDOW=15
RATE_LIMIT_MAX=100

# 日志配置
LOG_LEVEL=info
LOG_FILE=./logs/app.log

# 邮件配置（可选）
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-email-password
```

#### `docker-compose.yml`

```yaml
version: '3.8'

services:
  backend:
    build: ./backend
    ports:
      - "3000:3000"
    environment:
      - NODE_ENV=production
      - DB_PATH=/app/database/family.db
      - JWT_SECRET=${JWT_SECRET}
      - CLIENT_URL=${CLIENT_URL}
    volumes:
      - ./database:/app/database
      - ./uploads:/app/uploads
      - ./logs:/app/logs
    restart: unless-stopped
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:3000/health"]
      interval: 30s
      timeout: 10s
      retries: 3

  frontend:
    build: ./frontend
    ports:
      - "8080:80"
    volumes:
      - ./frontend/public:/usr/share/nginx/html
    restart: unless-stopped
    depends_on:
      - backend

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - ./ssl:/etc/nginx/ssl
    depends_on:
      - backend
      - frontend
    restart: unless-stopped

volumes:
  database_data:
  uploads_data:
  logs_data:
```

#### `nginx.conf`

```nginx
events {
    worker_connections 1024;
}

http {
    upstream backend {
        server backend:3000;
    }

    upstream frontend {
        server frontend:80;
    }

    server {
        listen 80;
        server_name familytree.example.com;
        
        # 重定向到HTTPS
        return 301 https://$server_name$request_uri;
    }

    server {
        listen 443 ssl http2;
        server_name familytree.example.com;
        
        # SSL证书
        ssl_certificate /etc/nginx/ssl/certificate.crt;
        ssl_certificate_key /etc/nginx/ssl/private.key;
        
        ssl_protocols TLSv1.2 TLSv1.3;
        ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
        ssl_prefer_server_ciphers off;
        
        # 安全头
        add_header X-Frame-Options "SAMEORIGIN" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header X-XSS-Protection "1; mode=block" always;
        add_header Referrer-Policy "strict-origin-when-cross-origin" always;
        add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
        
        # 前端文件
        location / {
            proxy_pass http://frontend;
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection 'upgrade';
            proxy_set_header Host $host;
            proxy_cache_bypass $http_upgrade;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }
        
        # API代理
        location /api {
            proxy_pass http://backend;
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection 'upgrade';
            proxy_set_header Host $host;
            proxy_cache_bypass $http_upgrade;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
            
            # 增加超时时间
            proxy_connect_timeout 60s;
            proxy_send_timeout 60s;
            proxy_read_timeout 60s;
        }
        
        # 上传文件
        location /uploads {
            alias /app/uploads;
            expires 30d;
            add_header Cache-Control "public, immutable";
            
            # 安全设置
            location ~ \.php$ {
                deny all;
            }
        }
        
        # Gzip压缩
        gzip on;
        gzip_vary on;
        gzip_min_length 1024;
        gzip_types text/plain text/css text/xml text/javascript 
                   application/javascript application/xml+rss 
                   application/json image/svg+xml;
    }
}
```

#### `README.md`

```markdown
# 族谱管理系统

一个完整的家族树管理系统，用于记录、管理和展示家族信息。

## ✨ 功能特性

- 🌳 交互式家族树可视化
- 👥 家族成员信息管理
- 📷 家族相册管理
- 📝 家族博客系统
- 🔐 用户认证和权限管理
- 📱 响应式设计
- 🔄 数据导入导出
- 📊 统计信息展示

## 🚀 快速开始

### 环境要求

- Node.js >= 14.0.0
- SQLite3
- npm 或 yarn

### 安装步骤

1. 克隆项目
```bash
git clone https://github.com/yourusername/family-tree.git
cd family-tree
```

2. 安装依赖
```bash
# 后端依赖
cd backend
npm install

# 前端依赖（可选，使用CDN）
# 如果需要本地构建，请安装前端依赖
```

3. 配置环境变量
```bash
cp .env.example .env
# 编辑.env文件，配置相关参数
```

4. 初始化数据库
```bash
cd backend
npm run migrate
```

5. 启动服务
```bash
# 开发模式
npm run dev

# 生产模式
npm start
```

6. 访问应用
```
前端：http://localhost:8080
后端API：http://localhost:3000
```

### Docker部署

1. 构建和启动容器
```bash
docker-compose up -d
```

2. 查看运行状态
```bash
docker-compose ps
```

3. 查看日志
```bash
docker-compose logs -f
```

## 📖 使用指南

### 管理员账号

默认管理员账号：
- 用户名：admin
- 密码：admin123

首次登录后请立即修改密码。

### 数据管理

1. **添加成员**
   - 点击"添加成员"按钮
   - 填写成员信息
   - 上传头像（可选）
   - 保存即可添加

2. **管理关系**
   - 在成员详情页编辑关系
   - 可以设置父子、夫妻等关系
   - 关系会自动反映在家族树中

3. **上传照片**
   - 支持批量上传
   - 可以为照片添加标签
   - 关联到具体成员

### API文档

API文档位于：`http://localhost:3000/api/docs`

主要接口：
- `GET /api/members` - 获取成员列表
- `POST /api/members` - 添加成员
- `GET /api/photos` - 获取照片列表
- `POST /api/photos/upload` - 上传照片
- `GET /api/blogs` - 获取博客列表
- `POST /api/blogs` - 发布博客

## 🗂️ 项目结构

```
族谱管理系统/
├── frontend/              # 前端代码
│   ├── public/           # 静态文件
│   └── assets/           # 资源文件
├── backend/              # 后端代码
│   ├── src/             # 源代码
│   ├── routes/          # 路由定义
│   ├── middleware/      # 中间件
│   └── models/          # 数据模型
├── database/             # 数据库文件
├── uploads/              # 上传文件
├── tests/                # 测试文件
└── docs/                 # 文档
```

## 🧪 测试

运行测试：
```bash
cd backend
npm test
```

测试覆盖率：
```bash
npm run test:coverage
```

## 🔧 配置说明

### 数据库配置

支持SQLite和MySQL，默认使用SQLite。如需使用MySQL，修改数据库配置：

```javascript
// database.js
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: 'password',
    database: 'family_tree'
});
```

### 文件上传配置

修改`.env`文件中的上传配置：
```env
UPLOAD_DIR=./uploads
MAX_FILE_SIZE=10mb
ALLOWED_FILE_TYPES=image/jpeg,image/png,image/gif
```

### 邮件配置（可选）

如需邮件功能，配置SMTP：
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password
```

## 📊 数据备份

### 手动备份
```bash
# 备份数据库
sqlite3 database/family.db .dump > backup.sql

# 备份上传文件
tar -czf uploads_backup.tar.gz uploads/
```

### 自动备份

系统支持自动备份，配置备份计划：
```bash
# 编辑crontab
crontab -e

# 添加备份任务（每天凌晨2点）
0 2 * * * /path/to/project/scripts/backup.sh
```

## 🤝 贡献指南

1. Fork 项目
2. 创建功能分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 开启 Pull Request

## 📄 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情。

## 🆘 技术支持

遇到问题？请：

1. 查看 [文档](docs/)
2. 搜索 [Issues](https://github.com/yourusername/family-tree/issues)
3. 创建新的 Issue

## 🙏 致谢

感谢以下开源项目：
- [ECharts](https://echarts.apache.org/) - 数据可视化
- [Tailwind CSS](https://tailwindcss.com/) - CSS框架
- [Express.js](https://expressjs.com/) - Node.js框架
- [SQLite](https://sqlite.org/) - 嵌入式数据库

## 📞 联系方式

项目维护者：[Your Name]
邮箱：support@familytree.com
网站：[https://familytree.com](https://familytree.com)
```

## 🚀 部署说明

### 方法一：本地部署（推荐开发使用）

1. **解压项目文件**
   ```bash
   unzip family-tree-system.zip
   cd family-tree-system
   ```

2. **安装后端依赖**
   ```bash
   cd backend
   npm install
   ```

3. **配置环境变量**
   ```bash
   cp .env.example .env
   # 编辑.env文件，配置数据库路径、JWT密钥等
   ```

4. **启动后端服务**
   ```bash
   npm start
   # 或开发模式: npm run dev
   ```

5. **访问前端页面**
   - 在浏览器中打开 `frontend/public/index.html`
   - 或使用本地服务器（如VS Code Live Server）

### 方法二：Docker部署（推荐生产使用）

1. **安装Docker和Docker Compose**
   ```bash
   # Ubuntu/Debian
   sudo apt-get update
   sudo apt-get install docker.io docker-compose
   ```

2. **配置环境变量**
   ```bash
   cp .env.example .env
   # 设置生产环境配置
   ```

3. **启动所有服务**
   ```bash
   docker-compose up -d
   ```

4. **查看服务状态**
   ```bash
   docker-compose ps
   docker-compose logs -f
   ```

5. **访问应用**
   - 前端：http://localhost:8080
   - 后端API：http://localhost:3000

### 方法三：云服务器部署

1. **购买云服务器（推荐配置）**
   - CPU：2核
   - 内存：4GB
   - 存储：50GB SSD
   - 系统：Ubuntu 20.04 LTS

2. **连接到服务器**
   ```bash
   ssh username@your-server-ip
   ```

3. **安装必要软件**
   ```bash
   sudo apt update
   sudo apt install nodejs npm nginx sqlite3
   ```

4. **上传项目文件**
   ```bash
   scp -r family-tree-system.zip username@your-server-ip:/home/username/
   ssh username@your-server-ip
   unzip family-tree-system.zip
   ```

5. **部署步骤**
   ```bash
   # 1. 安装后端依赖
   cd /home/username/family-tree-system/backend
   npm install --production
   
   # 2. 配置Nginx
   sudo cp nginx.conf /etc/nginx/sites-available/family-tree
   sudo ln -s /etc/nginx/sites-available/family-tree /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl reload nginx
   
   # 3. 配置PM2（进程管理）
   npm install -g pm2
   pm2 start src/server.js --name family-tree-api
   pm2 save
   pm2 startup
   
   # 4. 配置防火墙
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   sudo ufw allow 3000/tcp
   sudo ufw enable
   
   # 5. 配置SSL证书（可选）
   sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d your-domain.com
   ```

## 📦 打包下载

您可以将整个项目目录打包成ZIP文件：

```bash
# 在项目根目录执行
zip -r family-tree-system.zip . -x "node_modules/*" ".git/*" "uploads/*" "logs/*"
```

这个ZIP文件包含：
- ✅ 完整的前端代码（HTML/CSS/JavaScript）
- ✅ 完整的后端API（Node.js/Express）
- ✅ 数据库配置（SQLite）
- ✅ Docker部署配置
- ✅ Nginx配置
- ✅ 详细的使用文档
- ✅ 测试用例

## 🔧 常见问题

### Q1: 数据库连接失败
**解决方法：**
1. 检查SQLite数据库文件路径
2. 确保数据库目录有写权限
3. 重新初始化数据库：`npm run migrate`

### Q2: 上传文件失败
**解决方法：**
1. 检查uploads目录权限
2. 检查文件大小限制
3. 验证文件类型

### Q3: 前端无法连接后端API
**解决方法：**
1. 检查后端服务是否运行
2. 查看CORS配置
3. 检查网络连接

### Q4: 页面显示异常
**解决方法：**
1. 清除浏览器缓存
2. 检查JavaScript控制台错误
3. 验证网络请求

## 📞 技术支持

如有问题，请：
1. 查看项目文档
2. 检查控制台错误信息
3. 查看服务器日志
4. 提交Issue到GitHub

## 🎯 下一步计划

1. **移动端应用**
   - 开发React Native或Flutter应用
   - 离线数据同步
   - 推送通知

2. **高级功能**
   - AI人脸识别自动分类照片
   - 家族DNA数据分析
   - 时间线展示家族历史

3. **社交功能**
   - 家族聊天室
   - 活动组织
   - 礼物清单

4. **数据分析**
   - 家族健康状况分析
   - 职业分布统计
   - 地理分布图

## 📄 许可证

本项目采用MIT许可证，您可以自由使用、修改和分发。

---
