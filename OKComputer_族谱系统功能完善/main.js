// 家族谱系管理系统 - 主要JavaScript逻辑

// 全局变量
let familyData = {
    members: [],
    photos: [],
    blogs: []
};

let currentEditingMember = null;
let currentEditingPhoto = null;
let currentEditingBlog = null;
let photoCarousel = null;
let isAdmin = false;

// 初始化函数
document.addEventListener('DOMContentLoaded', async function() {
    // 检查管理员登录状态
    isAdmin = localStorage.getItem('adminLoggedIn') === 'true';
    
    // 如果没有登录且不是管理员页面，跳转到登录页
    const currentPage = window.location.pathname.split('/').pop();
    if (!isAdmin && currentPage === 'admin.html') {
        // 在管理员登录页面不做处理
    } else if (!isAdmin && currentPage !== 'admin.html') {
        // 其他页面需要检查管理员权限
        const isPublicPage = ['index.html', ''].includes(currentPage);
        if (!isPublicPage) {
            window.location.href = 'admin.html';
            return;
        }
    }
    
    await initializeApp();
});

async function initializeApp() {
    await initializeDatabase();
    initializeEventListeners();
    initializeParticles();
    
    // 根据当前页面初始化相应功能
    const currentPage = window.location.pathname.split('/').pop();
    switch(currentPage) {
        case 'index.html':
        case '':
            initializeHomePage();
            break;
        case 'members.html':
            initializeMembersPage();
            break;
        case 'gallery.html':
            initializeGalleryPage();
            break;
        case 'blogs.html':
            initializeBlogsPage();
            break;
        case 'spouses.html':
            initializeSpousesPage();
            break;
    }
}

// SQLite数据库变量
let db;

// 数据管理函数（localStorage回退方案）
function loadDataFromStorage() {
    const savedData = localStorage.getItem('familyTreeData');
    if (savedData) {
        familyData = JSON.parse(savedData);
    } else {
        // 初始化示例数据
        initializeSampleData();
    }
}

function saveDataToStorage() {
    localStorage.setItem('familyTreeData', JSON.stringify(familyData));
}

// 数据库初始化函数
async function initializeDatabase() {
    // 暂时直接使用localStorage存储方式
    console.log('使用localStorage存储方式');
    loadDataFromStorage();
    return;
}

// 从数据库加载数据
function loadDataFromDatabase() {
    // 加载成员数据
    const members = db.exec(`SELECT * FROM members`);
    if (members[0] && members[0].values) {
        familyData.members = members[0].values.map(row => {
            return {
                id: row[0],
                name: row[1],
                gender: row[2],
                birthDate: row[3],
                generation: row[4],
                phone: row[5],
                wechat: row[6],
                email: row[7],
                location: row[8],
                description: row[9],
                avatar: row[10],
                parents: row[11] ? JSON.parse(row[11]) : [],
                spouse: row[12] ? JSON.parse(row[12]) : null,
                children: row[13] ? JSON.parse(row[13]) : [],
                createdAt: row[14],
                updatedAt: row[15]
            };
        });
    }
    
    // 加载照片数据
    const photos = db.exec(`SELECT * FROM photos`);
    if (photos[0] && photos[0].values) {
        familyData.photos = photos[0].values.map(row => {
            return {
                id: row[0],
                title: row[1],
                description: row[2],
                imageUrl: row[3],
                date: row[4],
                uploader: row[5],
                tags: row[6] ? JSON.parse(row[6]) : [],
                createdAt: row[7],
                updatedAt: row[8]
            };
        });
    }
    
    // 加载博客数据
    const blogs = db.exec(`SELECT * FROM blogs`);
    if (blogs[0] && blogs[0].values) {
        familyData.blogs = blogs[0].values.map(row => {
            return {
                id: row[0],
                title: row[1],
                content: row[2],
                category: row[3],
                author: row[4],
                date: row[5],
                likes: row[6] || 0,
                comments: row[7] ? JSON.parse(row[7]) : [],
                createdAt: row[8],
                updatedAt: row[9]
            };
        });
    }
}

// 保存数据到数据库
function saveDataToDatabase() {
    // 如果数据库未初始化，使用localStorage回退
    if (!db) {
        console.log('数据库未初始化，使用localStorage保存数据');
        saveDataToStorage();
        return;
    }
    
    try {
        // 开始事务
        db.run('BEGIN TRANSACTION');
        
        // 清空现有数据
        db.run('DELETE FROM members');
        db.run('DELETE FROM photos');
        db.run('DELETE FROM blogs');
        
        // 保存成员数据
        const memberStmt = db.prepare(`
            INSERT INTO members (
                id, name, gender, birthDate, generation, phone, wechat, email, 
                location, description, avatar, parents, spouse, children, createdAt, updatedAt
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        `);
        
        familyData.members.forEach(member => {
            memberStmt.run(
                member.id,
                member.name,
                member.gender,
                member.birthDate,
                member.generation,
                member.phone,
                member.wechat,
                member.email,
                member.location,
                member.description,
                member.avatar,
                JSON.stringify(member.parents || []),
                member.spouse ? JSON.stringify(member.spouse) : null,
                JSON.stringify(member.children || []),
                member.createdAt || new Date().toISOString(),
                new Date().toISOString()
            );
        });
        
        memberStmt.free();
        
        // 保存照片数据
        const photoStmt = db.prepare(`
            INSERT INTO photos (
                id, title, description, imageUrl, date, uploader, tags, createdAt, updatedAt
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        `);
        
        familyData.photos.forEach(photo => {
            photoStmt.run(
                photo.id,
                photo.title,
                photo.description,
                photo.imageUrl,
                photo.date,
                photo.uploader,
                JSON.stringify(photo.tags || []),
                photo.createdAt || new Date().toISOString(),
                new Date().toISOString()
            );
        });
        
        photoStmt.free();
        
        // 保存博客数据
        const blogStmt = db.prepare(`
            INSERT INTO blogs (
                id, title, content, category, author, date, likes, comments, createdAt, updatedAt
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        `);
        
        familyData.blogs.forEach(blog => {
            blogStmt.run(
                blog.id,
                blog.title,
                blog.content,
                blog.category,
                blog.author,
                blog.date,
                blog.likes || 0,
                JSON.stringify(blog.comments || []),
                blog.createdAt || new Date().toISOString(),
                new Date().toISOString()
            );
        });
        
        blogStmt.free();
        
        // 提交事务
        db.run('COMMIT');
    } catch (error) {
        console.error('保存数据失败:', error);
        // 回滚事务
        if (db) {
            db.run('ROLLBACK');
        }
        // 回退到localStorage
        console.log('数据库保存失败，使用localStorage回退');
        saveDataToStorage();
    }
}

function initializeSampleData() {
    // 示例家族成员数据
    familyData.members = [
        {
            id: 'member_1',
            name: '王万',
            gender: 'male',
            birthDate: '1940-03-15',
            generation: 1,
            avatar: 'https://kimi-web-img.moonshot.cn/img/studycli.org/879a004360b169b52c22fb31abf8a0bd147dfa83.webp',
            phone: '13800000001',
            wechat: 'wangwan',
            email: 'wangwan@example.com',
            location: '北京',
            description: '王氏家族的长辈，德高望重。',
            parents: [],
            spouse: 'member_2',
            children: ['member_3', 'member_4', 'member_5']
        },
        {
            id: 'member_2',
            name: '李万',
            gender: 'female',
            birthDate: '1942-07-20',
            generation: 1,
            avatar: 'https://kimi-web-img.moonshot.cn/img/orientalmestore.com/1146d4400b974f788b0d3db163c9f62eb3cc9661.webp',
            phone: '13800000002',
            wechat: 'liwan',
            email: 'liwan@example.com',
            location: '北京',
            description: '王万的妻子，慈祥可亲。',
            parents: [],
            spouse: 'member_1',
            children: ['member_3', 'member_4', 'member_5']
        },
        {
            id: 'member_3',
            name: '王己',
            gender: 'male',
            birthDate: '1965-11-08',
            generation: 2,
            avatar: 'https://kimi-web-img.moonshot.cn/img/thumbs.dreamstime.com/f7c3996d9dfe1a63e6c70a50ab3279ce54778878.jpg',
            phone: '13800000003',
            wechat: 'wangji',
            email: 'wangji@example.com',
            location: '上海',
            description: '王万和李万的长子。',
            parents: ['member_1', 'member_2'],
            spouse: 'member_6',
            children: ['member_7', 'member_8']
        },
        {
            id: 'member_4',
            name: '王二',
            gender: 'male',
            birthDate: '1968-05-12',
            generation: 2,
            avatar: 'https://kimi-web-img.moonshot.cn/img/en.chinaculture.org/822d59af1088b616807a01d7951a3a80a4c38e66.jpg',
            phone: '13800000004',
            wechat: 'wanger',
            email: 'wanger@example.com',
            location: '广州',
            description: '王万和李万的次子。',
            parents: ['member_1', 'member_2'],
            spouse: '',
            children: []
        },
        {
            id: 'member_5',
            name: '王三',
            gender: 'female',
            birthDate: '1970-09-25',
            generation: 2,
            avatar: 'https://kimi-web-img.moonshot.cn/img/www.newhanfu.com/bd7e4b1038279b256f8a4e9fa87109c10286a0a8.jpg',
            phone: '13800000005',
            wechat: 'wangsan',
            email: 'wangsan@example.com',
            location: '深圳',
            description: '王万和李万的女儿。',
            parents: ['member_1', 'member_2'],
            spouse: 'member_9',
            children: ['member_10']
        },
        {
            id: 'member_6',
            name: '赵二',
            gender: 'female',
            birthDate: '1967-09-25',
            generation: 2,
            avatar: 'https://kimi-web-img.moonshot.cn/img/chinaculturecorner.com/147ff153b7e66a69190edc95625b6bd46be60ff9.jpg',
            phone: '13800000006',
            wechat: 'zhaoer',
            email: 'zhaoer@example.com',
            location: '上海',
            description: '王己的妻子。',
            parents: [],
            spouse: 'member_3',
            children: ['member_7', 'member_8']
        },
        {
            id: 'member_7',
            name: '王六',
            gender: 'male',
            birthDate: '1990-12-03',
            generation: 3,
            avatar: 'https://kimi-web-img.moonshot.cn/img/en.chinaculture.org/822d59af1088b616807a01d7951a3a80a4c38e66.jpg',
            phone: '13800000007',
            wechat: 'wangliu',
            email: 'wangliu@example.com',
            location: '上海',
            description: '王己和赵二的儿子。',
            parents: ['member_3', 'member_6'],
            spouse: '',
            children: []
        },
        {
            id: 'member_8',
            name: '王七',
            gender: 'female',
            birthDate: '1992-05-12',
            generation: 3,
            avatar: 'https://kimi-web-img.moonshot.cn/img/www.newhanfu.com/bd7e4b1038279b256f8a4e9fa87109c10286a0a8.jpg',
            phone: '13800000008',
            wechat: 'wangqi',
            email: 'wangqi@example.com',
            location: '上海',
            description: '王己和赵二的女儿。',
            parents: ['member_3', 'member_6'],
            spouse: '',
            children: []
        },
        {
            id: 'member_9',
            name: '张二',
            gender: 'male',
            birthDate: '1966-12-03',
            generation: 2,
            avatar: 'https://kimi-web-img.moonshot.cn/img/en.chinaculture.org/822d59af1088b616807a01d7951a3a80a4c38e66.jpg',
            phone: '13800000009',
            wechat: 'zhanger',
            email: 'zhanger@example.com',
            location: '深圳',
            description: '王三的丈夫。',
            parents: [],
            spouse: 'member_5',
            children: ['member_10']
        },
        {
            id: 'member_10',
            name: '王五',
            gender: 'male',
            birthDate: '1995-03-15',
            generation: 3,
            avatar: 'https://kimi-web-img.moonshot.cn/img/en.chinaculture.org/822d59af1088b616807a01d7951a3a80a4c38e66.jpg',
            phone: '13800000010',
            wechat: 'wangwu',
            email: 'wangwu@example.com',
            location: '深圳',
            description: '王三和张二的儿子。',
            parents: ['member_5', 'member_9'],
            spouse: '',
            children: []
        }
    ];

    // 示例照片数据
    familyData.photos = [
        {
            id: 'photo_1',
            url: 'https://kimi-web-img.moonshot.cn/img/goodhartphotographyva.com/2353293690a252b3e14bafcc0fb297b084b2c386.jpg',
            title: '家族聚会合影',
            description: '2024年春节全家团聚的美好时光',
            uploader: 'member_3',
            uploadDate: '2024-02-10',
            category: 'family',
            tags: ['春节', '聚会', '团圆'],
            relatedMembers: ['member_1', 'member_2', 'member_3', 'member_4', 'member_5', 'member_6']
        },
        {
            id: 'photo_2',
            url: 'https://kimi-web-img.moonshot.cn/img/www.isabelleguillen.com/5b4794764dcf6c9e6a9eea44e4c75f944859f7eb.jpg',
            title: '爷爷奶奶的结婚纪念日',
            description: '庆祝爷爷奶奶60周年结婚纪念日',
            uploader: 'member_4',
            uploadDate: '2023-10-15',
            category: 'festival',
            tags: ['纪念日', '爷爷奶奶', '爱情'],
            relatedMembers: ['member_1', 'member_2']
        }
    ];

    // 示例博客数据
    familyData.blogs = [
        {
            id: 'blog_1',
            title: '我们家族的历史传承',
            summary: '记录我们家族从古至今的发展历程，传承家族文化。',
            content: '<h2>家族的起源</h2><p>我们家族的历史可以追溯到清朝末年...</p><h2>家族的发展</h2><p>经过几代人的努力，我们家族在各个领域都有了不错的发展...</p>',
            author: 'member_1',
            publishDate: '2024-01-15',
            category: 'family',
            tags: ['家族历史', '传承', '文化'],
            photos: ['photo_1'],
            likes: 12,
            comments: [
                {
                    id: 'comment_1',
                    author: 'member_3',
                    content: '写得很好，让我们更了解家族历史！',
                    date: '2024-01-16'
                }
            ]
        },
        {
            id: 'blog_2',
            title: '春节聚会的美好回忆',
            summary: '记录2024年春节家族聚会的温馨时刻和美好回忆。',
            content: '<h2>团圆的时刻</h2><p>春节是中华民族最重要的传统节日，也是家人团聚的时刻...</p><h2>美食与欢笑</h2><p>奶奶亲手做的年夜饭，满满的都是家的味道...</p>',
            author: 'member_4',
            publishDate: '2024-02-12',
            category: 'event',
            tags: ['春节', '聚会', '回忆'],
            photos: ['photo_1'],
            likes: 8,
            comments: []
        }
    ];

    saveDataToDatabase();
}

// 事件监听器初始化
function initializeEventListeners() {
    // 通用模态框关闭按钮
    document.querySelectorAll('[id$="Btn"]').forEach(btn => {
        if (btn.id.includes('close') || btn.id.includes('cancel')) {
            btn.addEventListener('click', function() {
                const modal = this.closest('.fixed');
                if (modal) {
                    modal.classList.add('hidden');
                }
            });
        }
    });
    
    // 头像上传相关事件
    const uploadAvatarBtn = document.getElementById('uploadAvatarBtn');
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    
    if (uploadAvatarBtn && avatarInput) {
        uploadAvatarBtn.addEventListener('click', function() {
            avatarInput.click();
        });
        
        avatarInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (avatarPreview) {
                        // 更新预览
                        const imgElement = avatarPreview.querySelector('img');
                        if (imgElement) {
                            imgElement.src = e.target.result;
                        } else {
                            avatarPreview.innerHTML = `<img src="${e.target.result}" alt="头像预览" class="w-full h-full object-cover">`;
                        }
                        
                        // 保存头像数据到当前编辑的成员
                        if (currentEditingMember) {
                            currentEditingMember.avatar = e.target.result;
                        }
                    }
                }
                
                reader.readAsDataURL(file);
            }
        });
    }
    
    // 成员表单提交处理
    const memberForm = document.getElementById('memberForm');
    if (memberForm) {
        memberForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 收集家族关系数据
            const fatherId = document.getElementById('formMemberFather').value;
            const motherId = document.getElementById('formMemberMother').value;
            const spouseId = document.getElementById('formMemberSpouse').value;
            
            // 构建parents数组
            const parents = [];
            if (fatherId) parents.push(fatherId);
            if (motherId) parents.push(motherId);
            
            // 如果有当前编辑的成员，保留其avatar属性
            let avatar = currentEditingMember ? currentEditingMember.avatar : null;
            
            const memberData = {
                name: document.getElementById('formMemberName').value.trim(),
                gender: document.getElementById('formMemberGender').value,
                birthDate: document.getElementById('formMemberBirthDate').value,
                generation: parseInt(document.getElementById('formMemberGeneration').value) || 1,
                phone: document.getElementById('formMemberPhone').value.trim(),
                wechat: document.getElementById('formMemberWechat').value.trim(),
                email: document.getElementById('formMemberEmail').value.trim(),
                location: document.getElementById('formMemberLocation').value.trim(),
                description: document.getElementById('formMemberDescription').value.trim(),
                parents: parents,
                spouse: spouseId || null,
                children: currentEditingMember ? currentEditingMember.children : [],
                avatar: avatar
            };
            
            if (!memberData.name) {
                alert('请输入成员姓名');
                return;
            }
            
            if (!memberData.gender) {
                alert('请选择成员性别');
                return;
            }
            
            saveMember(memberData);
            
            // 关闭模态框
            const modal = document.getElementById('memberFormModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        });
    }
}

// 粒子背景初始化（已禁用）
function initializeParticles() {
    // 粒子背景已禁用，以提升视觉清晰度
    const container = document.getElementById('particle-container');
    if (container) {
        container.style.display = 'none';
    }
}

// 主页初始化
function initializeHomePage() {
    initializeFamilyTree();
    initializeGenealogyChart();
    initializeGenealogyChartEvents();
    updateHomePageStats();
    updateLatestMembers();
    updateLatestActivities();
    updateAdminStatus();
    
    // 主页特定事件监听器
    const addMemberBtn = document.getElementById('addMemberBtn');
    if (addMemberBtn) {
        addMemberBtn.addEventListener('click', () => {
            if (checkAdminPermission()) {
                showAddMemberModal();
            }
        });
    }
    
    const expandTreeBtn = document.getElementById('expandTreeBtn');
    if (expandTreeBtn) {
        expandTreeBtn.addEventListener('click', () => expandFamilyTree());
    }
}

// 家族树初始化
// 家族树显示模式（'default' 或 'patrilineal'）
let familyTreeMode = 'default';

// 节点隐藏状态管理
let hiddenNodes = new Set();

// 切换节点隐藏/显示状态
function toggleNodeVisibility(memberId) {
    if (hiddenNodes.has(memberId)) {
        hiddenNodes.delete(memberId);
    } else {
        hiddenNodes.add(memberId);
    }
    // 重新初始化家族树以应用更改
    initializeFamilyTree();
}

function initializeFamilyTree() {
    const treeContainer = document.getElementById('familyTree');
    if (!treeContainer) return;
    
    const chart = echarts.init(treeContainer);
    
    // 添加全屏按钮事件监听器
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', () => {
            toggleFullscreen();
        });
    }
    
    // 添加节点隐藏/显示控制按钮
    const treeControls = document.getElementById('treeControls');
    if (treeControls && !document.getElementById('toggleVisibilityBtn')) {
        const toggleBtn = document.createElement('button');
        toggleBtn.id = 'toggleVisibilityBtn';
        toggleBtn.className = 'bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mr-2';
        toggleBtn.textContent = '切换节点可见性';
        toggleBtn.addEventListener('click', () => {
            // 显示节点选择对话框
            showNodeVisibilityDialog();
        });
        treeControls.appendChild(toggleBtn);
    }
    
    // 添加调试信息
    console.log('王氏家谱数据:', familyData.members);
    
    // 转换数据为树形结构
    const treeData = familyTreeMode === 'patrilineal' ? convertToPatrilinealTreeData() : convertToTreeData();
    console.log('转换后的树形数据:', treeData);
    
    // 添加模式切换按钮
    const modeToggleBtn = document.getElementById('treeModeToggle');
    if (!modeToggleBtn) {
        const treeControls = document.getElementById('treeControls');
        if (treeControls) {
            const toggleBtn = document.createElement('button');
            toggleBtn.id = 'treeModeToggle';
            toggleBtn.className = 'bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded mr-2';
            toggleBtn.textContent = familyTreeMode === 'patrilineal' ? '切换到标准模式' : '切换到父系模式';
            
            toggleBtn.addEventListener('click', () => {
                familyTreeMode = familyTreeMode === 'patrilineal' ? 'default' : 'patrilineal';
                toggleBtn.textContent = familyTreeMode === 'patrilineal' ? '切换到标准模式' : '切换到父系模式';
                initializeFamilyTree(); // 重新初始化家族树
            });
            
            treeControls.insertBefore(toggleBtn, treeControls.firstChild);
        }
    } else {
        modeToggleBtn.textContent = familyTreeMode === 'patrilineal' ? '切换到标准模式' : '切换到父系模式';
    }
    
    const option = {
        tooltip: {
            trigger: 'item',
            triggerOn: 'mousemove',
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: '#ccc',
            borderWidth: 1,
            borderRadius: 8,
            padding: 10,
            boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)',
            formatter: function(params) {
                const data = params.data;
                if (!data || !data.member) return '';
                
                const member = data.member;
                let content = `
                    <div class="tooltip-content">
                        <h4 class="font-semibold text-lg text-amber-900 flex items-center">
                            ${member.gender === 'male' ? '👨' : '👩'} ${member.name}
                        </h4>
                        <p class="text-sm text-gray-600 mt-1">${member.birthDate || ''}</p>
                        ${member.deathDate ? `<p class="text-sm text-gray-500">去世: ${member.deathDate}</p>` : ''}
                        ${member.description ? `<p class="text-sm text-gray-700 mt-2">${member.description}</p>` : ''}
                        ${member.generation ? `<p class="text-xs text-gray-500 mt-2">第${member.generation}代</p>` : ''}
                    </div>
                `;
                
                // 如果有配偶，也显示配偶信息
                if (data.spouse) {
                    const spouse = data.spouse;
                    content += `
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <h4 class="font-semibold text-amber-900 flex items-center">
                                ${spouse.gender === 'male' ? '👨' : '👩'} 配偶: ${spouse.name}
                            </h4>
                            <p class="text-sm text-gray-600 mt-1">${spouse.birthDate || ''}</p>
                            ${spouse.deathDate ? `<p class="text-sm text-gray-500">去世: ${spouse.deathDate}</p>` : ''}
                        </div>
                    `;
                }
                
                return content;
            }
        },
        series: [
            {
                type: 'tree',
                data: [treeData],
                top: '5%',
                left: '18%',
                bottom: '5%',
                right: '5%',
                symbolSize: function(params) {
                    // 如果有配偶，使用较大的尺寸
                    if (params && params.data) {
                        return params.data.spouse ? [130, 55] : [85, 55];
                    }
                    return [85, 55];
                },
                symbol: 'rect',
                symbolRotate: 0,
                symbolOffset: [0, 0],
                symbolKeepAspect: false,
                edgeSymbol: ['circle', 'arrow'],
                edgeSymbolSize: [5, 12],
                layout: 'orthogonal', // 设置正交布局
                orient: 'TB', // 从上到下布局
                levelGap: 150, // 增加层级之间的垂直间距，提供更多空间
                nodeGap: 50, // 增加同层节点之间的水平间距，避免拥挤
                leaves: {
                    orient: 'LR', // 叶子节点（第三代）从左到右布局
                    nodeGap: 60 // 叶子节点之间的间距更大
                },
                label: {
                    position: 'inside',
                    verticalAlign: 'middle',
                    align: 'center',
                    fontSize: 16,
                    fontWeight: 'bold',
                    fontFamily: '"Microsoft YaHei", "PingFang SC", "Hiragino Sans GB", "Helvetica Neue", Arial, sans-serif',
                    // 使用深蓝色字体，与浅蓝色背景形成良好对比
                    color: '#0d47a1',
                    rich: {
                        name: {
                            fontSize: 16,
                            fontWeight: 'bold',
                            lineHeight: 24,
                            color: '#0d47a1' // 深蓝色姓名
                        },
                        icon: {
                            fontSize: 18,
                            padding: [0, 4, 0, 0],
                            color: '#0d47a1' // 深蓝色图标
                        },
                        generation: {
                            fontSize: 12,
                            fontWeight: 'bold',
                            color: '#fff', // 白色世代数字
                            backgroundColor: '#4a90e2', // 统一使用蓝色背景
                            borderRadius: 5,
                            padding: [2, 5, 2, 5],
                            margin: [0, 0, 0, 5]
                        },
                        separator: {
                            fontSize: 14,
                            color: '#90caf9', // 浅蓝色分隔线
                            padding: [4, 0, 4, 0]
                        }
                    },
                    formatter: function(params) {
                        const data = params.data;
                        if (!data.member) return params.name;
                        
                        // 格式化姓名和性别
                        const member = data.member;
                        // 使用更明显的性别图标
                        const genderIcon = member.gender === 'male' ? '👨' : '👩';
                        
                        // 如果有配偶，显示夫妻信息，用分隔线区分
                        if (data.spouse) {
                            const spouse = data.spouse;
                            const spouseIcon = spouse.gender === 'male' ? '👨' : '👩';
                            const generationText = member.generation ? ` ${member.generation}代` : '';
                            // 使用更美观的分隔方式，世代标识更明显
                            return `{icon|${genderIcon}} {name|${member.name}} {generation|${generationText}}\n{separator|${'—'.repeat(16)}}\n{icon|${spouseIcon}} {name|${spouse.name}}`;
                        }
                        
                        // 单个成员
                        const generationText = member.generation ? ` ${member.generation}代` : '';
                        return `{icon|${genderIcon}} {name|${member.name}} {generation|${generationText}}`;
                    }
                },
                leaves: {
                    label: {
                        position: 'inside',
                        verticalAlign: 'middle',
                        align: 'center',
                        fontSize: 15,
                        fontWeight: 'bold',
                        fontFamily: '"Microsoft YaHei", "PingFang SC", "Hiragino Sans GB", "Helvetica Neue", Arial, sans-serif',
                        color: '#0d47a1', // 统一使用深蓝色字体，与主节点保持一致
                        rich: {
                            name: {
                                fontSize: 15,
                                fontWeight: 'bold',
                                lineHeight: 22,
                                color: '#0d47a1' // 统一深蓝色姓名
                            },
                            icon: {
                                fontSize: 17,
                                padding: [0, 4, 0, 0],
                                color: '#0d47a1' // 统一深蓝色图标
                            },
                            generation: {
                                fontSize: 11,
                                fontWeight: 'bold',
                                color: '#fff', // 白色世代数字
                                backgroundColor: '#4a90e2', // 蓝色背景，与主节点保持一致
                                borderRadius: 5,
                                padding: [2, 5, 2, 5],
                                margin: [0, 0, 0, 5]
                            },
                            separator: {
                                fontSize: 13,
                                color: '#90caf9', // 浅蓝色分隔线，与主节点保持一致
                                padding: [3, 0, 3, 0]
                            }
                        },
                        formatter: function(params) {
                            const data = params.data;
                            if (!data.member) return params.name;
                            
                            const member = data.member;
                            const genderIcon = member.gender === 'male' ? '👨' : '👩';
                            const generationText = member.generation ? ` (${member.generation}代)` : '';
                            
                            if (data.spouse) {
                                const spouse = data.spouse;
                                const spouseIcon = spouse.gender === 'male' ? '👨' : '👩';
                                return `{icon|${genderIcon}} {name|${member.name}} {generation|${generationText}}\n{separator|${'—'.repeat(15)}}\n{icon|${spouseIcon}} {name|${spouse.name}}`;
                            }
                            
                            return `{icon|${genderIcon}} {name|${member.name}} {generation|${generationText}}`;
                        }
                    }
                },
                emphasis: {
                    focus: 'descendant',
                    scale: true, // 启用缩放效果
                    scaleSize: 10, // 缩放大小
                    itemStyle: {
                        borderWidth: 6, // 增加边框宽度
                        borderColor: '#ff9800',
                        shadowBlur: 20, // 增加阴影模糊度
                        shadowColor: 'rgba(255, 152, 0, 0.7)', // 增加阴影透明度
                        shadowOffsetX: 8,
                        shadowOffsetY: 8
                    },
                    label: {
                        fontSize: 18, // 增大字体
                        fontWeight: 'bold',
                        textShadowBlur: 8,
                        textShadowColor: 'rgba(0, 0, 0, 0.5)'
                    },
                    animation: true,
                    animationDuration: 300,
                    animationEasing: 'backOut'
                },
                expandAndCollapse: true,
                animationDuration: 1200,
                animationDurationUpdate: 1500,
                animationEasing: 'elasticOut',
                animationEasingUpdate: 'backOut',
                animationDelay: function(idx) {
                    return idx * 50;
                },
                animationDelayUpdate: function(idx) {
                    return idx * 30;
                },
                itemStyle: {
                    color: function(params) {
                        // 优化浅蓝色渐变背景，增加更丰富的层次感
                        return new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#f0f8ff' },    // 顶部极浅蓝，增加明亮感
                            { offset: 0.3, color: '#e6f3ff' },  // 上中部浅蓝
                            { offset: 0.7, color: '#b3e0ff' },  // 下中部主色
                            { offset: 1, color: '#66c2ff' }     // 底部深蓝，增强深度感
                        ]);
                    },
                    borderColor: function(params) {
                        // 根据节点层级设置边框颜色，增强层次感
                        const depth = params.data.level || 0;
                        const borderColors = ['#3399ff', '#4a90e2', '#66b3ff', '#80c8ff', '#99d6ff'];
                        return borderColors[depth % borderColors.length];
                    },
                    borderWidth: 3, // 略微增加边框宽度，使节点更加突出
                    borderRadius: 15, // 增加圆角，使节点更加圆润柔和
                    borderType: 'solid', // 统一使用实线边框
                    shadowBlur: 20, // 增强阴影模糊度，提升立体感
                    shadowColor: 'rgba(102, 179, 255, 0.5)', // 增强阴影透明度和范围
                    shadowOffsetX: 5,
                    shadowOffsetY: 5,
                    // 添加内阴影效果，增强节点的凹陷感
                    opacity: 0.95 // 略微降低透明度，使整体视觉更加柔和
                },
                lineStyle: {
                    // 根据层级设置不同的连接线样式，与节点颜色协调
                    color: function(params) {
                        // 使用与节点边框颜色相协调的蓝色系
                        const depth = params.data.level || 0;
                        const lineColors = ['#3399ff', '#4a90e2', '#66b3ff', '#80c8ff', '#99d6ff'];
                        return lineColors[depth % lineColors.length];
                    },
                    width: function(params) {
                        // 根据层级调整线宽，根节点最粗，越往下越细，增强层次感
                        const depth = params.data.level || 0;
                        // 使用更平滑的线宽递减
                        return Math.max(2.5, 5.5 - depth * 0.8);
                    },
                    type: 'curved',
                    curveness: function(params) {
                        // 根据层级调整弯曲度，优化曲线效果
                        const depth = params.data.level || 0;
                        // 根节点连接线更直，中间层级弯曲度适中，深层级保持一定弯曲度
                        if (depth === 0) return 0.1;
                        if (depth === 1) return 0.25;
                        if (depth === 2) return 0.4;
                        return 0.5;
                    },
                    opacity: function(params) {
                        // 根据层级调整透明度，保持良好的视觉层次感
                        const depth = params.data.level || 0;
                        return Math.max(0.85, 1.0 - depth * 0.03);
                    },
                    cap: 'round', // 线条端点使用圆角，更柔和
                    join: 'round', // 线条交点使用圆角，更柔和
                    shadowBlur: function(params) {
                        // 添加轻微阴影，增强连接线的立体感
                        const depth = params.data.level || 0;
                        return Math.max(3, 8 - depth);
                    },
                    shadowColor: 'rgba(102, 179, 255, 0.4)', // 连接线阴影颜色，与节点阴影协调
                    shadowOffsetX: 2,
                    shadowOffsetY: 2
                },
                initialTreeDepth: -1 // 默认展开所有节点
            }
        ]
    };
    
    chart.setOption(option);
    
    // 添加点击事件
    chart.on('click', function(params) {
        if (params.data && params.data.member) {
            showMemberDetail(params.data.member);
        }
    });
    
    // 缩放控制
    let currentZoom = 1;
    const minZoom = 0.5;
    const maxZoom = 2;
    
    const zoomInBtn = document.getElementById('zoomInBtn');
    const zoomOutBtn = document.getElementById('zoomOutBtn');
    
    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', () => {
            setZoom(currentZoom * 1.1);
        });
    }
    
    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', () => {
            setZoom(currentZoom * 0.9);
        });
    }
    
    // 鼠标滚轮缩放
    treeContainer.addEventListener('wheel', function(e) {
        e.preventDefault();
        const zoomFactor = e.deltaY > 0 ? 0.9 : 1.1;
        const newZoom = currentZoom * zoomFactor;
        setZoom(newZoom);
    });
    
    // 设置缩放函数
    function setZoom(zoom) {
        currentZoom = Math.max(minZoom, Math.min(maxZoom, zoom));
        chart.setOption({
            graphic: {
                elements: [{
                    type: 'group',
                    id: 'zoom-group',
                    style: {
                        transform: `scale(${currentZoom})`,
                        transformOrigin: 'center center'
                    }
                }]
            }
        });
    }
}

// 全屏切换功能
function toggleFullscreen() {
    const treeContainer = document.getElementById('familyTree');
    if (!treeContainer) return;
    
    // 检查浏览器是否支持全屏API
    if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement && !document.msFullscreenElement) {
        // 进入全屏模式
        if (treeContainer.requestFullscreen) {
            treeContainer.requestFullscreen();
        } else if (treeContainer.webkitRequestFullscreen) {
            treeContainer.webkitRequestFullscreen();
        } else if (treeContainer.mozRequestFullScreen) {
            treeContainer.mozRequestFullScreen();
        } else if (treeContainer.msRequestFullscreen) {
            treeContainer.msRequestFullscreen();
        }
    } else {
        // 退出全屏模式
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
}

// 监听全屏变化事件
document.addEventListener('fullscreenchange', updateChartSize);
document.addEventListener('webkitfullscreenchange', updateChartSize);
document.addEventListener('mozfullscreenchange', updateChartSize);
document.addEventListener('MSFullscreenChange', updateChartSize);

// 节点可见性对话框
function showNodeVisibilityDialog() {
    // 创建对话框容器
    const dialogContainer = document.createElement('div');
    dialogContainer.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    dialogContainer.id = 'nodeVisibilityDialog';
    
    // 创建对话框内容
    const dialogContent = document.createElement('div');
    dialogContent.className = 'bg-white rounded-lg shadow-xl p-6 w-3/4 max-h-[80vh] overflow-y-auto';
    
    // 对话框标题
    const dialogTitle = document.createElement('h3');
    dialogTitle.className = 'text-xl font-bold mb-4 text-amber-900';
    dialogTitle.textContent = '选择要隐藏的节点';
    
    // 节点列表
    const nodeList = document.createElement('div');
    nodeList.className = 'space-y-2';
    
    // 渲染所有成员节点的复选框
    familyData.members.forEach(member => {
        const nodeItem = document.createElement('div');
        nodeItem.className = 'flex items-center p-2 hover:bg-gray-100 rounded';
        
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = `node_${member.id}`;
        checkbox.className = 'mr-3';
        checkbox.checked = hiddenNodes.has(member.id);
        checkbox.addEventListener('change', () => {
            toggleNodeVisibility(member.id);
        });
        
        const label = document.createElement('label');
        label.htmlFor = `node_${member.id}`;
        label.className = 'flex-1 text-gray-800';
        label.innerHTML = `${member.gender === 'male' ? '👨' : '👩'} ${member.name} (${member.generation}代)`;
        
        const spouseInfo = document.createElement('span');
        spouseInfo.className = 'text-sm text-gray-500';
        if (member.spouse) {
            const spouse = familyData.members.find(m => m.id === member.spouse);
            if (spouse) {
                spouseInfo.textContent = `配偶: ${spouse.name}`;
            }
        }
        
        nodeItem.appendChild(checkbox);
        nodeItem.appendChild(label);
        nodeItem.appendChild(spouseInfo);
        nodeList.appendChild(nodeItem);
    });
    
    // 关闭按钮
    const closeBtn = document.createElement('button');
    closeBtn.className = 'mt-4 bg-amber-600 hover:bg-amber-700 text-white px-6 py-2 rounded float-right';
    closeBtn.textContent = '关闭';
    closeBtn.addEventListener('click', () => {
        dialogContainer.remove();
    });
    
    // 清空选择按钮
    const clearBtn = document.createElement('button');
    clearBtn.className = 'mt-4 bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded float-left';
    clearBtn.textContent = '显示所有节点';
    clearBtn.addEventListener('click', () => {
        hiddenNodes.clear();
        initializeFamilyTree();
        dialogContainer.remove();
    });
    
    // 组装对话框
    dialogContent.appendChild(dialogTitle);
    dialogContent.appendChild(nodeList);
    dialogContent.appendChild(clearBtn);
    dialogContent.appendChild(closeBtn);
    dialogContainer.appendChild(dialogContent);
    
    // 添加到页面
    document.body.appendChild(dialogContainer);
    
    // 点击背景关闭对话框
    dialogContainer.addEventListener('click', (e) => {
        if (e.target === dialogContainer) {
            dialogContainer.remove();
        }
    });
}

// 更新图表大小函数
function updateChartSize() {
    const chart = echarts.getInstanceByDom(document.getElementById('familyTree'));
    if (chart) {
        chart.resize();
    }
}

// 转换成员数据为树形结构（默认模式）
function convertToTreeData() {
    if (familyData.members.length === 0) return {};
    
    // 创建处理过的成员集合，避免重复处理
    const processedMembers = new Set();
    
    // 处理成员节点，将配偶信息添加到节点中但不合并节点
    const processMember = (member) => {
        // 检查节点是否被隐藏
        if (hiddenNodes.has(member.id)) return null;
        
        if (processedMembers.has(member.id)) return null;
        
        processedMembers.add(member.id);
        
        const node = {
            name: member.name,
            member: member,
            children: buildChildrenTree(member.id)
        };
        
        // 如果有配偶，将配偶信息添加到节点中
        if (member.spouse) {
            const spouse = familyData.members.find(m => m.id === member.spouse);
            if (spouse && !processedMembers.has(spouse.id) && !hiddenNodes.has(spouse.id)) {
                node.spouse = spouse;
                processedMembers.add(spouse.id); // 标记配偶为已处理
            }
        }
        
        return node;
    };
    
    // 找到根节点（没有父母的成员）
    // 过滤掉第二代及以上且没有父母的成员，避免显示为单独节点
    const potentialRoots = familyData.members.filter(member => 
        (!member.parents || member.parents.length === 0) && 
        (member.generation === 1 || member.generation === undefined)
    );
    
    // 处理潜在根节点，确保配偶关系中只有一个作为根节点
    const rootMembers = [];
    const spousePairs = new Set();
    
    potentialRoots.forEach(member => {
        // 如果成员已经在配偶对集合中，跳过
        if (spousePairs.has(member.id)) return;
        
        // 将该成员作为根节点
        rootMembers.push(member);
        
        // 如果有配偶，将配偶添加到已处理集合
        if (member.spouse) {
            spousePairs.add(member.spouse);
        }
    });
    
    if (rootMembers.length === 0) {
        // 如果没有明确的根节点，选择第一个成员作为根
        const firstMember = familyData.members[0];
        return processMember(firstMember);
    }
    
    // 如果有多个根节点，创建虚拟根
    if (rootMembers.length > 1) {
        return {
            name: '',
            member: null,
            children: rootMembers.map(member => processMember(member)).filter(n => n !== null)
        };
    }
    
    // 单个根节点
    const rootMember = rootMembers[0];
    const rootNode = processMember(rootMember);
    
    // 如果是单个成员，添加家谱标题
    if (rootNode) {
        return {
            name: '',
            member: null,
            children: [rootNode]
        };
    }
    
    return {};
}

// 转换成员数据为父系为主的树形结构
function convertToPatrilinealTreeData() {
    if (familyData.members.length === 0) return {};
    
    // 找到最早的男性祖先（始祖）
    let earliestMaleAncestor = null;
    let earliestGeneration = Infinity;
    
    familyData.members.forEach(member => {
        if (member.gender === 'male' && member.generation < earliestGeneration) {
            earliestMaleAncestor = member;
            earliestGeneration = member.generation;
        }
    });
    
    if (!earliestMaleAncestor) {
        // 如果没有男性祖先，使用默认模式
        return convertToTreeData();
    }
    
    // 构建父系树
    function buildPatrilinealTree(memberId, includeSpouse = true) {
        const member = familyData.members.find(m => m.id === memberId);
        if (!member) return null;
        
        // 检查节点是否被隐藏
        if (hiddenNodes.has(member.id)) return null;
        
        const treeNode = {
            name: member.name,
            member: member,
            children: []
        };
        
        // 添加配偶
        if (includeSpouse && member.spouse) {
            const spouse = familyData.members.find(m => m.id === member.spouse);
            if (spouse && !hiddenNodes.has(spouse.id)) {
                treeNode.spouse = {
                    name: spouse.name,
                    member: spouse
                };
            }
        }
        
        // 添加儿子（父系传承）
        const sons = familyData.members.filter(m => 
            m.parents && m.parents.includes(memberId) && m.gender === 'male'
        );
        
        sons.forEach(son => {
            const sonNode = buildPatrilinealTree(son.id);
            if (sonNode) {
                treeNode.children.push(sonNode);
            }
        });
        
        // 如果没有儿子，添加女儿（仅作为叶节点）
        if (treeNode.children.length === 0) {
            const daughters = familyData.members.filter(m => 
                m.parents && m.parents.includes(memberId) && m.gender === 'female'
            );
            
            daughters.forEach(daughter => {
                treeNode.children.push({
                    name: daughter.name,
                    member: daughter,
                    children: []
                });
            });
        }
        
        return treeNode;
    }
    
    return buildPatrilinealTree(earliestMaleAncestor.id);
}

// 构建子节点树（默认模式）
function buildChildrenTree(parentId) {
    const children = familyData.members.filter(member => 
        member.parents && member.parents.includes(parentId)
    );
    
    return children.map(child => {
        // 检查节点是否被隐藏
        if (hiddenNodes.has(child.id)) return null;
        
        const node = {
            name: child.name,
            member: child,
            children: buildChildrenTree(child.id)
        };
        
        // 如果有配偶，将配偶信息添加到节点中（配偶未隐藏）
        if (child.spouse) {
            const spouse = familyData.members.find(m => m.id === child.spouse);
            if (spouse && !hiddenNodes.has(spouse.id)) {
                node.spouse = spouse;
            }
        }
        
        return node;
    }).filter(node => node !== null); // 过滤掉null节点
}

// 更新主页统计信息
function updateHomePageStats() {
    const totalMembersEl = document.getElementById('totalMembers');
    const totalGenerationsEl = document.getElementById('totalGenerations');
    const totalPhotosEl = document.getElementById('totalPhotos');
    const totalBlogsEl = document.getElementById('totalBlogs');
    
    if (totalMembersEl) totalMembersEl.textContent = familyData.members.length;
    if (totalPhotosEl) totalPhotosEl.textContent = familyData.photos.length;
    if (totalBlogsEl) totalBlogsEl.textContent = familyData.blogs.length;
    
    // 计算世代数
    const generations = new Set(familyData.members.map(member => member.generation));
    if (totalGenerationsEl) totalGenerationsEl.textContent = generations.size;
}

// 更新最新成员
function updateLatestMembers() {
    const container = document.getElementById('latestMembers');
    if (!container) return;
    
    const latestMembers = familyData.members
        .sort((a, b) => new Date(b.birthDate || 0) - new Date(a.birthDate || 0))
        .slice(0, 3);
    
    container.innerHTML = latestMembers.map(member => `
        <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-amber-50 transition-colors cursor-pointer"
             onclick="showMemberDetail(familyData.members.find(m => m.id === '${member.id}'))">
            <div class="w-10 h-10 rounded-full bg-amber-200 flex items-center justify-center overflow-hidden">
                ${member.avatar ? 
                    `<img src="${member.avatar}" alt="${member.name}" class="w-full h-full object-cover">` :
                    `<span class="text-amber-800 font-semibold">${member.name.charAt(0)}</span>`
                }
            </div>
            <div class="flex-1">
                <h4 class="font-medium text-gray-900">${member.name}</h4>
                <p class="text-sm text-gray-600">${member.birthDate || ''}</p>
            </div>
        </div>
    `).join('');
}

// 更新最新动态展示
function updateLatestActivities() {
    const container = document.getElementById('latestActivities');
    if (!container) return;
    
    // 创建博客活动数组
    const blogActivities = familyData.blogs.map(blog => ({
        id: blog.id,
        type: 'blog',
        title: blog.title,
        author: blog.author,
        date: blog.date || blog.createdAt,
        content: blog.content,
        category: blog.category
    }));
    
    // 创建照片活动数组
    const photoActivities = familyData.photos.map(photo => ({
        id: photo.id,
        type: 'photo',
        title: photo.title,
        uploader: photo.uploader,
        date: photo.uploadDate,
        imageUrl: photo.url,
        description: photo.description
    }));
    
    // 合并并按日期排序
    const allActivities = [...blogActivities, ...photoActivities]
        .sort((a, b) => new Date(b.date || 0) - new Date(a.date || 0))
        .slice(0, 8);
    
    // 生成HTML
    container.innerHTML = allActivities.map(activity => {
        // 获取作者/上传者信息
        const personId = activity.type === 'blog' ? activity.author : activity.uploader;
        const person = familyData.members.find(m => m.id === personId);
        const personName = person ? person.name : '未知用户';
        
        // 格式化日期
        const formattedDate = new Date(activity.date).toLocaleDateString();
        
        if (activity.type === 'blog') {
            // 博客活动卡片 - 优化宽屏显示
            const previewContent = activity.content ? activity.content.substring(0, 100) + '...' : '';
            return `
                <div class="p-4 rounded-lg hover:bg-amber-50 transition-colors cursor-pointer border border-amber-200 hover:border-amber-400"
                     onclick="showBlogDetail('${activity.id}')">
                    <div class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
                        <!-- 博客图标和类型 -->
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- 博客内容 -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-amber-800">博客</span>
                                    <span class="text-xs text-gray-400">·</span>
                                    <span class="text-xs text-gray-500">${formattedDate}</span>
                                </div>
                                <span class="text-xs text-gray-500">${personName}</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mt-1 mb-2">${activity.title}</h4>
                            <p class="text-sm text-gray-600 line-clamp-2">${previewContent}</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // 照片活动卡片 - 优化宽屏显示
            return `
                <div class="p-4 rounded-lg hover:bg-blue-50 transition-colors cursor-pointer border border-blue-200 hover:border-blue-400"
                     onclick="showPhotoViewer('${activity.id}')">
                    <div class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
                        <!-- 照片缩略图 -->
                        <div class="flex-shrink-0">
                            <img src="${activity.imageUrl}" alt="${activity.title}" 
                                 class="w-20 h-20 object-cover rounded-md shadow-sm border border-blue-100">
                        </div>
                        
                        <!-- 照片信息 -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-blue-800">照片</span>
                                    <span class="text-xs text-gray-400">·</span>
                                    <span class="text-xs text-gray-500">${formattedDate}</span>
                                </div>
                                <span class="text-xs text-gray-500">${personName}</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mt-1 mb-2">${activity.title}</h4>
                            <p class="text-sm text-gray-600 line-clamp-2">${activity.description || '无描述'}</p>
                        </div>
                    </div>
                </div>
            `;
        }
    }).join('');
}

// 成员管理页面初始化
function initializeMembersPage() {
    // 检查管理员权限
    if (!checkAdminPermission()) {
        return;
    }
    
    renderMembersGrid();
    initializeMemberFilters();
    updateAdminStatus();
    
    // 添加成员按钮
    const addNewMemberBtn = document.getElementById('addNewMemberBtn');
    if (addNewMemberBtn) {
        addNewMemberBtn.addEventListener('click', () => showAddMemberModal());
    }
    
    // 搜索功能
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterMembers);
    }
    
    // Excel导入功能
    const importExcelBtn = document.getElementById('importExcelBtn');
    if (importExcelBtn) {
        importExcelBtn.addEventListener('click', showExcelImportModal);
    }
    
    // 下载模板按钮
    const downloadTemplateBtn = document.getElementById('downloadTemplateBtn');
    if (downloadTemplateBtn) {
        downloadTemplateBtn.addEventListener('click', downloadTemplate);
    }
    
    // Excel导入模态框关闭按钮
    const closeExcelImportModalBtn = document.getElementById('closeExcelImportModalBtn');
    if (closeExcelImportModalBtn) {
        closeExcelImportModalBtn.addEventListener('click', closeExcelImportModal);
    }
    
    // 选择Excel文件按钮
    const selectExcelFileBtn = document.getElementById('selectExcelFileBtn');
    if (selectExcelFileBtn) {
        selectExcelFileBtn.addEventListener('click', () => {
            document.getElementById('excelFileInput').click();
        });
    }
    
    // Excel文件选择事件
    const excelFileInput = document.getElementById('excelFileInput');
    if (excelFileInput) {
        excelFileInput.addEventListener('change', handleExcelFileSelect);
    }
    
    // 确认导入按钮
    const confirmImportBtn = document.getElementById('confirmImportBtn');
    if (confirmImportBtn) {
        confirmImportBtn.addEventListener('click', importExcelData);
    }
    
    // 导出JSON按钮
    const exportJSONBtn = document.getElementById('exportJSONBtn');
    if (exportJSONBtn) {
        exportJSONBtn.addEventListener('click', exportJSON);
    }
    
    // 导出Excel按钮
    const exportExcelBtn = document.getElementById('exportExcelBtn');
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', exportExcelData);
    }
}

// 渲染成员网格
function renderMembersGrid() {
    const container = document.getElementById('membersGrid');
    const emptyState = document.getElementById('emptyState');
    
    if (!container) return;
    
    if (familyData.members.length === 0) {
        container.classList.add('hidden');
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    if (emptyState) emptyState.classList.add('hidden');
    
    container.innerHTML = familyData.members.map(member => `
        <div class="member-card bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6 cursor-pointer"
             onclick="showMemberDetail(familyData.members.find(m => m.id === '${member.id}'))">
            <div class="text-center mb-4">
                <div class="w-20 h-20 mx-auto rounded-full bg-amber-200 flex items-center justify-center overflow-hidden mb-4">
                    ${member.avatar ? 
                        `<img src="${member.avatar}" alt="${member.name}" class="w-full h-full object-cover">` :
                        `<span class="text-amber-800 font-bold text-2xl">${member.name.charAt(0)}</span>`
                    }
                </div>
                <h3 class="hero-title text-xl font-semibold text-amber-900 mb-1">${member.name}</h3>
                <p class="text-gray-600 text-sm">${member.gender === 'male' ? '男' : '女'} · 第${member.generation || 1}代</p>
            </div>
            
            ${member.birthDate ? `
                <div class="mb-3">
                    <p class="text-sm text-gray-600 mb-1">出生日期</p>
                    <p class="text-sm font-medium">${member.birthDate}</p>
                </div>
            ` : ''}
            
            ${member.location ? `
                <div class="mb-3">
                    <p class="text-sm text-gray-600 mb-1">现居地</p>
                    <p class="text-sm font-medium">${member.location}</p>
                </div>
            ` : ''}
            
            ${member.description ? `
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-1">个人简介</p>
                    <p class="text-sm text-gray-800 line-clamp-2">${member.description}</p>
                </div>
            ` : ''}
            
            <div class="flex space-x-2">
                <button class="flex-1 bg-amber-100 hover:bg-amber-200 text-amber-800 py-2 px-3 rounded-lg text-sm font-medium transition-colors"
                        onclick="event.stopPropagation(); editMember('${member.id}')">
                    编辑
                </button>
                <button class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-3 rounded-lg text-sm font-medium transition-colors"
                        onclick="event.stopPropagation(); showMemberDetail(familyData.members.find(m => m.id === '${member.id}'))">
                    详情
                </button>
            </div>
        </div>
    `).join('');
}

// 初始化成员筛选器
function initializeMemberFilters() {
    const genderFilter = document.getElementById('genderFilter');
    const generationFilter = document.getElementById('generationFilter');
    const patrilinealBranchFilter = document.getElementById('patrilinealBranchFilter');
    const patrilinealDepthFilter = document.getElementById('patrilinealDepthFilter');
    
    if (genderFilter) {
        genderFilter.addEventListener('change', filterMembers);
    }
    
    if (generationFilter) {
        generationFilter.addEventListener('change', filterMembers);
    }
    
    if (patrilinealBranchFilter) {
        // 动态生成父系分支选项
        generatePatrilinealBranchOptions();
        patrilinealBranchFilter.addEventListener('change', filterMembers);
    }
    
    if (patrilinealDepthFilter) {
        patrilinealDepthFilter.addEventListener('change', filterMembers);
    }
}

// 生成父系分支选项
function generatePatrilinealBranchOptions() {
    const branchFilter = document.getElementById('patrilinealBranchFilter');
    if (!branchFilter) return;
    
    // 收集所有唯一的分支ID
    const uniqueBranchIds = [...new Set(familyData.members.map(member => member.patrilineal?.branchId).filter(id => id))];
    
    // 清空现有选项（保留默认选项）
    const defaultOption = branchFilter.querySelector('option[value=""]');
    branchFilter.innerHTML = '';
    branchFilter.appendChild(defaultOption);
    
    // 添加分支选项
    uniqueBranchIds.forEach(branchId => {
        const branchHead = familyData.members.find(member => member.id === branchId);
        if (branchHead) {
            const option = document.createElement('option');
            option.value = branchId;
            option.textContent = `${branchHead.name} 分支`;
            branchFilter.appendChild(option);
        }
    });
}

// 筛选成员
function filterMembers() {
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const genderFilter = document.getElementById('genderFilter')?.value || '';
    const generationFilter = document.getElementById('generationFilter')?.value || '';
    const patrilinealBranchFilter = document.getElementById('patrilinealBranchFilter')?.value || '';
    const patrilinealDepthFilter = document.getElementById('patrilinealDepthFilter')?.value || '';
    
    const filteredMembers = familyData.members.filter(member => {
        const matchesSearch = member.name.toLowerCase().includes(searchTerm) ||
                            member.description?.toLowerCase().includes(searchTerm);
        const matchesGender = !genderFilter || member.gender === genderFilter;
        const matchesGeneration = !generationFilter || member.generation == generationFilter;
        const matchesBranch = !patrilinealBranchFilter || member.patrilineal?.branchId === patrilinealBranchFilter;
        
        // 父系深度筛选
        let matchesDepth = true;
        if (patrilinealDepthFilter) {
            const depth = parseInt(patrilinealDepthFilter);
            const memberDepth = member.patrilineal?.depth || 0;
            
            if (depth === 0) {
                // 父系祖先（深度小于等于当前成员）
                matchesDepth = familyData.members.every(m => 
                    m.patrilineal?.depth === undefined || 
                    m.id === member.id || 
                    !member.patrilineal?.ancestors.includes(m.id) || 
                    m.patrilineal.depth <= memberDepth
                );
            } else if (depth === 1) {
                // 父系后代（深度大于等于当前成员）
                matchesDepth = familyData.members.every(m => 
                    m.patrilineal?.depth === undefined || 
                    m.id === member.id || 
                    !member.patrilineal?.descendants.includes(m.id) || 
                    m.patrilineal.depth >= memberDepth
                );
            } else if (depth === 2) {
                // 远房父系（同分支但非直接祖先或后代）
                matchesDepth = member.patrilineal?.branchId && 
                             familyData.members.some(m => 
                                 m.id !== member.id && 
                                 m.patrilineal?.branchId === member.patrilineal.branchId && 
                                 !member.patrilineal?.ancestors.includes(m.id) && 
                                 !member.patrilineal?.descendants.includes(m.id)
                             );
            }
        }
        
        return matchesSearch && matchesGender && matchesGeneration && matchesBranch && matchesDepth;
    });
    
    const container = document.getElementById('membersGrid');
    if (!container) return;
    
    // 重新渲染筛选后的成员
    container.innerHTML = filteredMembers.map(member => `
        <div class="member-card bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6 cursor-pointer"
             onclick="showMemberDetail(familyData.members.find(m => m.id === '${member.id}'))">
            <div class="text-center mb-4">
                <div class="w-20 h-20 mx-auto rounded-full bg-amber-200 flex items-center justify-center overflow-hidden mb-4">
                    ${member.avatar ? 
                        `<img src="${member.avatar}" alt="${member.name}" class="w-full h-full object-cover">` :
                        `<span class="text-amber-800 font-bold text-2xl">${member.name.charAt(0)}</span>`
                    }
                </div>
                <h3 class="hero-title text-xl font-semibold text-amber-900 mb-1">${member.name}</h3>
                <p class="text-gray-600 text-sm">${member.gender === 'male' ? '男' : '女'} · 第${member.generation || 1}代</p>
                ${member.patrilineal?.branchId ? `
                <p class="text-xs text-amber-600 mt-1">${familyData.members.find(m => m.id === member.patrilineal.branchId)?.name || ''} 分支</p>
                ` : ''}
            </div>
            
            ${member.birthDate ? `
                <div class="mb-3">
                    <p class="text-sm text-gray-600 mb-1">出生日期</p>
                    <p class="text-sm font-medium">${member.birthDate}</p>
                </div>
            ` : ''}
            
            ${member.location ? `
                <div class="mb-3">
                    <p class="text-sm text-gray-600 mb-1">现居地</p>
                    <p class="text-sm font-medium">${member.location}</p>
                </div>
            ` : ''}
            
            ${member.description ? `
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-1">个人简介</p>
                    <p class="text-sm text-gray-800 line-clamp-2">${member.description}</p>
                </div>
            ` : ''}
            
            <div class="flex space-x-2">
                <button class="flex-1 bg-amber-100 hover:bg-amber-200 text-amber-800 py-2 px-3 rounded-lg text-sm font-medium transition-colors"
                        onclick="event.stopPropagation(); editMember('${member.id}')">
                    编辑
                </button>
                <button class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-3 rounded-lg text-sm font-medium transition-colors"
                        onclick="event.stopPropagation(); showMemberDetail(familyData.members.find(m => m.id === '${member.id}'))">
                    详情
                </button>
            </div>
        </div>
    `).join('');
}

// 显示成员详情
function showMemberDetail(member) {
    const modal = document.getElementById('memberDetailModal');
    const nameEl = document.getElementById('detailMemberName');
    const contentEl = document.getElementById('memberDetailContent');
    
    if (!modal || !nameEl || !contentEl) return;
    
    nameEl.textContent = member.name;
    
    // 获取家族关系
    const parents = member.parents && member.parents.length > 0 ? member.parents.map(parentId => {
        return familyData.members.find(m => m.id === parentId);
    }).filter(Boolean) : [];
    
    const spouse = member.spouse ? familyData.members.find(m => m.id === member.spouse) : null;
    
    const children = familyData.members.filter(m => m.parents && m.parents.includes(member.id));
    
    const siblings = familyData.members.filter(m => {
        if (!m.parents || !member.parents) return false;
        // 至少有一个共同的父母
        return m.parents.some(parentId => member.parents.includes(parentId)) && m.id !== member.id;
    });
    
    contentEl.innerHTML = `
        <!-- 头部信息卡片 -->
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-6 mb-6 shadow-md">
            <div class="flex items-center space-x-6">
                <div class="w-24 h-24 rounded-full bg-white border-4 border-amber-300 flex items-center justify-center overflow-hidden shadow-lg">
                    ${member.avatar ? 
                        `<img src="${member.avatar}" alt="${member.name}" class="w-full h-full object-cover">` :
                        `<span class="text-amber-700 font-bold text-3xl">${member.name.charAt(0)}</span>`
                    }
                </div>
                <div class="flex-1">
                    <h4 class="text-2xl font-bold text-amber-900">${member.name}</h4>
                    <p class="text-lg text-gray-600 mt-1">
                        <span class="px-3 py-1 bg-${member.gender === 'male' ? 'blue' : 'red'}-100 text-${member.gender === 'male' ? 'blue' : 'red'}-800 rounded-full text-sm font-medium">
                            ${member.gender === 'male' ? '男' : '女'}
                        </span>
                        <span class="ml-3">第${member.generation || 1}代 · ${member.ranking || 1}排行</span>
                    </p>
                    ${member.deceased ? `
                        <p class="text-red-600 text-sm mt-1">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            已去世
                        </p>
                    ` : ''}
                </div>
            </div>
        </div>
        
        <!-- 基本信息卡片 -->
        <div class="bg-white rounded-xl p-6 mb-6 shadow-md border border-amber-100">
            <h5 class="text-lg font-semibold text-amber-900 mb-4 pb-2 border-b border-amber-200">
                基本信息
            </h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                ${member.birthDate ? `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">出生日期</p>
                            <p class="font-medium text-gray-800">${member.birthDate}</p>
                        </div>
                    </div>
                ` : ''}
                
                ${member.phone ? `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">手机号</p>
                            <p class="font-medium text-gray-800">${member.phone}</p>
                        </div>
                    </div>
                ` : ''}
                
                ${member.wechat ? `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">微信号</p>
                            <p class="font-medium text-gray-800">${member.wechat}</p>
                        </div>
                    </div>
                ` : ''}
                
                ${member.email ? `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">邮箱</p>
                            <p class="font-medium text-gray-800">${member.email}</p>
                        </div>
                    </div>
                ` : ''}
                
                ${member.location ? `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">现居地</p>
                            <p class="font-medium text-gray-800">${member.location}</p>
                        </div>
                    </div>
                ` : ''}
                
                ${member.nativePlace ? `
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">出生地</p>
                            <p class="font-medium text-gray-800">${member.nativePlace}</p>
                        </div>
                    </div>
                ` : ''}
            </div>
        </div>
        
        <!-- 个人简介 -->
        ${member.description ? `
            <div class="bg-white rounded-xl p-6 mb-6 shadow-md border border-amber-100">
                <h5 class="text-lg font-semibold text-amber-900 mb-4 pb-2 border-b border-amber-200">
                    个人简介
                </h5>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">${member.description}</p>
            </div>
        ` : ''}
        
        <!-- 家族关系 -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-amber-100">
            <h5 class="text-lg font-semibold text-amber-900 mb-4 pb-2 border-b border-amber-200">
                家族关系
            </h5>
            
            <div class="space-y-5">
                <!-- 父母 -->
                ${parents.length > 0 ? `
                    <div>
                        <p class="text-sm font-medium text-amber-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            父母
                        </p>
                        <div class="flex flex-wrap gap-3">
                            ${parents.map(parent => `
                                <div class="bg-gray-50 px-4 py-2 rounded-lg flex items-center space-x-2 cursor-pointer hover:bg-gray-100 transition-colors"
                                    onclick="event.stopPropagation(); showMemberDetail(familyData.members.find(m => m.id === '${parent.id}'))">
                                    <span class="text-sm font-medium text-gray-800">${parent.name}</span>
                                    <span class="text-xs text-gray-500">(${parent.gender === 'male' ? '父' : '母'})</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
                
                <!-- 配偶 -->
                ${spouse ? `
                    <div>
                        <p class="text-sm font-medium text-amber-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            配偶
                        </p>
                        <div class="flex items-center space-x-2">
                            <div class="bg-gray-50 px-4 py-2 rounded-lg flex items-center space-x-2 cursor-pointer hover:bg-gray-100 transition-colors"
                                onclick="event.stopPropagation(); showMemberDetail(familyData.members.find(m => m.id === '${spouse.id}'))">
                                <span class="text-sm font-medium text-gray-800">${spouse.name}</span>
                                <span class="text-xs text-gray-500">(${spouse.gender === 'male' ? '夫' : '妻'})</span>
                            </div>
                        </div>
                    </div>
                ` : ''}
                
                <!-- 子女 -->
                ${children.length > 0 ? `
                    <div>
                        <p class="text-sm font-medium text-amber-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            子女 (${children.length})
                        </p>
                        <div class="flex flex-wrap gap-3">
                            ${children.map(child => `
                                <div class="bg-gray-50 px-4 py-2 rounded-lg flex items-center space-x-2 cursor-pointer hover:bg-gray-100 transition-colors"
                                    onclick="event.stopPropagation(); showMemberDetail(familyData.members.find(m => m.id === '${child.id}'))">
                                    <span class="text-sm font-medium text-gray-800">${child.name}</span>
                                    <span class="text-xs text-gray-500">(${child.gender === 'male' ? '子' : '女'})</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
                
                <!-- 兄弟姐妹 -->
                ${siblings.length > 0 ? `
                    <div>
                        <p class="text-sm font-medium text-amber-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            兄弟姐妹 (${siblings.length})
                        </p>
                        <div class="flex flex-wrap gap-3">
                            ${siblings.map(sibling => `
                                <div class="bg-gray-50 px-4 py-2 rounded-lg flex items-center space-x-2 cursor-pointer hover:bg-gray-100 transition-colors"
                                    onclick="event.stopPropagation(); showMemberDetail(familyData.members.find(m => m.id === '${sibling.id}'))">
                                    <span class="text-sm font-medium text-gray-800">${sibling.name}</span>
                                    <span class="text-xs text-gray-500">(${sibling.gender === 'male' ? '兄' : '姐'})</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    
    // 为编辑按钮绑定事件
    const editBtn = document.getElementById('editMemberBtn');
    if (editBtn) {
        editBtn.onclick = function() {
            editMember(member.id);
            modal.classList.add('hidden');
        };
    }
    
    // 为删除按钮绑定事件
    const deleteBtn = document.getElementById('deleteMemberBtn');
    if (deleteBtn) {
        deleteBtn.onclick = function() {
            deleteMember(member.id);
            modal.classList.add('hidden');
        };
    }
}

// 初始化家族关系选择框
function initializeFamilyRelationSelectors(excludeMemberId = null) {
    // 获取所有家族成员
    const members = familyData.members;
    
    // 1. 初始化世代选择框
    const generationSelect = document.getElementById('formMemberGeneration');
    if (generationSelect) {
        // 清空现有选项
        generationSelect.innerHTML = '<option value="">请选择</option>';
        
        // 计算当前存在的最大世代数
        const maxGeneration = members.length > 0 ? Math.max(...members.map(member => member.generation || 1)) : 1;
        
        // 添加当前存在的世代选项
        for (let i = 1; i <= maxGeneration + 2; i++) { // 显示到当前最大世代数+2，允许新增世代
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `第${i}代`;
            generationSelect.appendChild(option);
        }
    }
    
    // 2. 父亲选择框 - 只显示男性成员
    const fatherSelect = document.getElementById('formMemberFather');
    if (fatherSelect) {
        // 清空现有选项
        fatherSelect.innerHTML = '<option value="">请选择</option>';
        
        // 添加男性成员选项
        members.forEach(member => {
            if (member.id !== excludeMemberId && member.gender === 'male') {
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = member.name;
                fatherSelect.appendChild(option);
            }
        });
    }
    
    // 3. 母亲选择框 - 只显示女性成员
    const motherSelect = document.getElementById('formMemberMother');
    if (motherSelect) {
        // 清空现有选项
        motherSelect.innerHTML = '<option value="">请选择</option>';
        
        // 添加女性成员选项
        members.forEach(member => {
            if (member.id !== excludeMemberId && member.gender === 'female') {
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = member.name;
                motherSelect.appendChild(option);
            }
        });
    }
    
    // 4. 配偶选择框 - 显示所有异性成员
    const spouseSelect = document.getElementById('formMemberSpouse');
    if (spouseSelect) {
        // 清空现有选项
        spouseSelect.innerHTML = '<option value="">请选择</option>';
        
        // 如果是编辑模式，获取当前成员性别
        const currentMember = excludeMemberId ? members.find(m => m.id === excludeMemberId) : null;
        const currentGender = currentMember ? currentMember.gender : null;
        
        // 添加异性成员选项
        members.forEach(member => {
            if (member.id !== excludeMemberId && (!currentGender || member.gender !== currentGender)) {
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = member.name;
                spouseSelect.appendChild(option);
            }
        });
    }
}

// 显示添加成员模态框
function showAddMemberModal() {
    const modal = document.getElementById('addMemberModal') || document.getElementById('memberFormModal');
    if (!modal) return;
    
    currentEditingMember = null;
    
    // 重置表单
    const form = document.getElementById('addMemberForm') || document.getElementById('memberForm');
    if (form) {
        form.reset();
        document.getElementById('modalTitle').textContent = '添加家族成员';
        
        // 初始化家族关系选择框
        initializeFamilyRelationSelectors();
        
        // 表单提交事件已在其他地方绑定，避免重复提交
        // 不再重复绑定onsubmit事件
        
    }
    
    modal.classList.remove('hidden');
}

// 编辑成员
function editMember(memberId) {
    const member = familyData.members.find(m => m.id === memberId);
    if (!member) return;
    
    currentEditingMember = member;
    
    const modal = document.getElementById('memberFormModal');
    if (!modal) return;
    
    // 初始化家族关系选择框
    initializeFamilyRelationSelectors(memberId);
    
    // 填充表单
    document.getElementById('formMemberName').value = member.name || '';
    document.getElementById('formMemberGender').value = member.gender || '';
    document.getElementById('formMemberBirthDate').value = member.birthDate || '';
    document.getElementById('formMemberGeneration').value = member.generation || '';
    document.getElementById('formMemberPhone').value = member.phone || '';
    document.getElementById('formMemberWechat').value = member.wechat || '';
    document.getElementById('formMemberEmail').value = member.email || '';
    document.getElementById('formMemberLocation').value = member.location || '';
    document.getElementById('formMemberDescription').value = member.description || '';
    
    // 设置家族关系选择框的默认值
    if (member.parents && member.parents.length > 0) {
        const fatherId = member.parents.find(parentId => {
            const parent = familyData.members.find(m => m.id === parentId);
            return parent && parent.gender === 'male';
        });
        
        const motherId = member.parents.find(parentId => {
            const parent = familyData.members.find(m => m.id === parentId);
            return parent && parent.gender === 'female';
        });
        
        if (fatherId) {
            document.getElementById('formMemberFather').value = fatherId;
        }
        
        if (motherId) {
            document.getElementById('formMemberMother').value = motherId;
        }
    }
    
    // 设置配偶选择框的默认值
    if (member.spouse) {
        document.getElementById('formMemberSpouse').value = member.spouse;
    }
    
    document.getElementById('modalTitle').textContent = '编辑成员信息';
    modal.classList.remove('hidden');
    
    // 初始化头像预览
    const avatarPreview = document.getElementById('avatarPreview');
    if (avatarPreview) {
        if (member.avatar) {
            avatarPreview.innerHTML = `<img src="${member.avatar}" alt="${member.name}" class="w-full h-full object-cover">`;
        } else {
            avatarPreview.innerHTML = '<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>';
        }
    }

    // 为编辑按钮绑定事件
    const editBtn = document.getElementById('editBlogBtn');
    if (editBtn) {
        editBtn.onclick = function() {
            showBlogEditor(blog.id);
            modal.classList.add('hidden');
        };
    }
    
    // 为删除按钮绑定事件
    const deleteBtn = document.getElementById('deleteBlogBtn');
    if (deleteBtn) {
        deleteBtn.onclick = function() {
            deleteBlog(blog.id);
        };
    }
}

// 保存成员
// 父系数据完整性验证
function validateMemberData(formData) {
    const errors = [];
    
    // 1. 父母关系验证
    if (formData.parents && formData.parents.length > 0) {
        // 检查父亲和母亲的性别
        let fatherCount = 0;
        let motherCount = 0;
        
        formData.parents.forEach(parentId => {
            const parent = familyData.members.find(m => m.id === parentId);
            if (parent) {
                if (parent.gender === 'male') {
                    fatherCount++;
                } else {
                    motherCount++;
                }
                
                // 检查父母不能是自己
                if (parentId === formData.id) {
                    errors.push('不能将自己设为父母');
                }
                
                // 检查父母不能是后代
                if (parent.children && parent.children.includes(formData.id)) {
                    errors.push('不能将后代设为父母');
                }
                
                // 检查父母的世代必须小于当前成员
                if (formData.generation && parent.generation && parent.generation >= formData.generation) {
                    errors.push('父母的世代必须小于当前成员');
                }
            }
        });
        
        // 确保只有一个父亲和一个母亲
        if (fatherCount > 1) {
            errors.push('只能有一个父亲');
        }
        if (motherCount > 1) {
            errors.push('只能有一个母亲');
        }
    }
    
    // 2. 配偶关系验证
    if (formData.spouse) {
        const spouse = familyData.members.find(m => m.id === formData.spouse);
        if (spouse) {
            // 检查不能与自己结婚
            if (formData.spouse === formData.id) {
                errors.push('不能与自己结婚');
            }
            
            // 检查必须是异性
            if (spouse.gender === formData.gender) {
                errors.push('配偶必须是异性');
            }
            
            // 检查不能与父母结婚
            if (formData.parents && formData.parents.includes(formData.spouse)) {
                errors.push('不能与父母结婚');
            }
            
            // 检查不能与子女结婚
            if (spouse.parents && spouse.parents.includes(formData.id)) {
                errors.push('不能与子女结婚');
            }
        }
    }
    
    // 3. 世代验证
    if (formData.generation && formData.generation < 1) {
        errors.push('世代值必须大于0');
    }
    
    // 4. 父系完整性验证 - 确保有父亲（如果不是第一代）
    if (formData.generation > 1 && formData.parents) {
        const hasFather = formData.parents.some(parentId => {
            const parent = familyData.members.find(m => m.id === parentId);
            return parent && parent.gender === 'male';
        });
        
        if (!hasFather) {
            errors.push('非第一代成员必须有父亲');
        }
    }
    
    return errors;
}

function saveMember(formData) {
    // 自动计算世代 - 只在新增成员时执行，编辑成员时保留用户输入
    if (!currentEditingMember) {
        if (formData.parents && formData.parents.length > 0) {
            // 查找父亲
            const fatherId = formData.parents.find(parentId => {
                const parent = familyData.members.find(m => m.id === parentId);
                return parent && parent.gender === 'male';
            });
            
            if (fatherId) {
                const father = familyData.members.find(m => m.id === fatherId);
                if (father && father.generation) {
                    // 新成员的世代 = 父亲的世代 + 1
                    formData.generation = father.generation + 1;
                }
            }
        } else {
            // 如果没有父母，设置为始祖（始迁祖），世代为1
            formData.generation = 1;
        }
    }
    
    // 验证数据完整性
    const validationErrors = validateMemberData(formData);
    if (validationErrors.length > 0) {
        alert('保存失败：\n' + validationErrors.join('\n'));
        return;
    }
    
    if (currentEditingMember) {
        // 更新现有成员
        
        // 保存旧的关系数据，用于后续更新关联
        const oldParents = [...(currentEditingMember.parents || [])];
        const oldSpouse = currentEditingMember.spouse;
        
        // 更新基本信息
        Object.assign(currentEditingMember, formData);
        
        // 更新家族关系 - 父母
        updateParentChildRelations(currentEditingMember.id, oldParents, formData.parents);
        
        // 更新家族关系 - 配偶
        updateSpouseRelation(currentEditingMember.id, oldSpouse, formData.spouse);
        
    } else {
        // 添加新成员
        const newMember = {
            id: 'member_' + Date.now(),
            patrilineal: {
                ancestors: [],
                descendants: [],
                branchId: null,
                depth: 1
            },
            ...formData
        };
        familyData.members.push(newMember);
        
        // 设置新成员的家族关系 - 父母
        if (formData.parents && formData.parents.length > 0) {
            formData.parents.forEach(parentId => {
                const parent = familyData.members.find(m => m.id === parentId);
                if (parent && !parent.children) {
                    parent.children = [];
                }
                if (parent && !parent.children.includes(newMember.id)) {
                    parent.children.push(newMember.id);
                }
            });
        }
        
        // 设置新成员的家族关系 - 配偶
        if (formData.spouse) {
            const spouse = familyData.members.find(m => m.id === formData.spouse);
            if (spouse) {
                spouse.spouse = newMember.id;
            }
        }
    }
    
    saveDataToDatabase();
    
    // 刷新页面内容
    const currentPage = window.location.pathname.split('/').pop();
    if (currentPage === 'index.html' || currentPage === '') {
        updateHomePageStats();
        updateLatestMembers();
        initializeFamilyTree();
    } else if (currentPage === 'members.html') {
        renderMembersGrid();
    }
}

// 更新父母-子女关系
function updateParentChildRelations(memberId, oldParents, newParents) {
    // 移除旧的父母关系
    oldParents.forEach(parentId => {
        // 如果旧父母不在新父母列表中，移除关系
        if (!newParents.includes(parentId)) {
            // 从成员的父母列表中移除
            const member = familyData.members.find(m => m.id === memberId);
            if (member) {
                member.parents = member.parents.filter(id => id !== parentId);
            }
        }
    });
    
    // 添加新的父母关系
    newParents.forEach(parentId => {
        // 如果新父母不在旧父母列表中，添加关系
        if (!oldParents.includes(parentId)) {
            // 将成员添加到父母的子女列表
            const parent = familyData.members.find(m => m.id === parentId);
            if (parent) {
                if (!parent.children) parent.children = [];
                if (!parent.children.includes(memberId)) {
                    parent.children.push(memberId);
                }
            }
        }
    });
    
    // 更新父系血缘追踪信息
    updatePatrilinealInfo(memberId);
}

// 更新父系血缘追踪信息
function updatePatrilinealInfo(memberId) {
    const member = familyData.members.find(m => m.id === memberId);
    if (!member) return;
    
    // 计算父系祖先
    const patrilinealAncestors = [];
    let currentParent = member;
    
    // 追踪父系祖先（只考虑父亲）
    while (currentParent) {
        const father = currentParent.parents.find(parentId => {
            const parent = familyData.members.find(m => m.id === parentId);
            return parent && parent.gender === 'male';
        });
        
        if (father) {
            const fatherMember = familyData.members.find(m => m.id === father);
            if (fatherMember) {
                patrilinealAncestors.push(father);
                currentParent = fatherMember;
            } else {
                break;
            }
        } else {
            break;
        }
    }
    
    // 计算父系后代
    const patrilinealDescendants = [];
    
    function findPatrilinealDescendants(parentId) {
        const parent = familyData.members.find(m => m.id === parentId);
        if (!parent || !parent.children) return;
        
        parent.children.forEach(childId => {
            const child = familyData.members.find(m => m.id === childId);
            if (child) {
                // 儿子属于父系后代
                if (child.gender === 'male') {
                    patrilinealDescendants.push(childId);
                    // 递归查找儿子的后代
                    findPatrilinealDescendants(childId);
                }
                // 女儿的儿子也属于父系后代
                findPatrilinealDescendants(childId);
            }
        });
    }
    
    findPatrilinealDescendants(memberId);
    
    // 计算父系分支ID
    let patrilinealBranchId = null;
    if (member.gender === 'male') {
        // 对于男性成员，分支ID为其自身ID（如果有儿子的话）
        const hasMaleChildren = member.children && member.children.some(childId => {
            const child = familyData.members.find(m => m.id === childId);
            return child && child.gender === 'male';
        });
        
        if (hasMaleChildren) {
            patrilinealBranchId = member.id;
        } else {
            // 如果没有儿子，则使用父亲的分支ID
            const fatherId = member.parents.find(parentId => {
                const parent = familyData.members.find(m => m.id === parentId);
                return parent && parent.gender === 'male';
            });
            
            if (fatherId) {
                const father = familyData.members.find(m => m.id === fatherId);
                if (father && father.patrilineal) {
                    patrilinealBranchId = father.patrilineal.branchId;
                }
            }
        }
    } else {
        // 对于女性成员，使用父亲的分支ID
        const fatherId = member.parents.find(parentId => {
            const parent = familyData.members.find(m => m.id === parentId);
            return parent && parent.gender === 'male';
        });
        
        if (fatherId) {
            const father = familyData.members.find(m => m.id === fatherId);
            if (father && father.patrilineal) {
                patrilinealBranchId = father.patrilineal.branchId;
            }
        }
    }
    
    // 更新成员的父系血缘信息
    member.patrilineal = {
        ancestors: patrilinealAncestors,
        descendants: patrilinealDescendants,
        branchId: patrilinealBranchId,
        // 计算父系代数：1为自身，2为父亲，3为祖父，以此类推
        depth: patrilinealAncestors.length + 1
    };
    
    // 递归更新所有后代的父系信息
    if (member.children && member.children.length > 0) {
        member.children.forEach(childId => {
            updatePatrilinealInfo(childId);
        });
    }
}

// 计算两个成员之间的亲属关系
function calculateRelationship(memberId1, memberId2) {
    const member1 = familyData.members.find(m => m.id === memberId1);
    const member2 = familyData.members.find(m => m.id === memberId2);
    
    if (!member1 || !member2) return { relationship: '未知', bloodCoefficient: 0 };
    
    // 检查是否为同一个人
    if (memberId1 === memberId2) {
        return { relationship: '本人', bloodCoefficient: 1 };
    }
    
    // 检查父子/父女关系
    if (member1.children && member1.children.includes(memberId2)) {
        const relation = member2.gender === 'male' ? '儿子' : '女儿';
        return { relationship: relation, bloodCoefficient: 0.5 };
    }
    if (member2.children && member2.children.includes(memberId1)) {
        const relation = member1.gender === 'male' ? '父亲' : '母亲';
        return { relationship: relation, bloodCoefficient: 0.5 };
    }
    
    // 检查兄弟姐妹关系
    const commonParents = member1.parents.filter(parentId => member2.parents.includes(parentId));
    if (commonParents.length > 0) {
        if (member1.gender === 'male' && member2.gender === 'male') {
            return { relationship: '兄弟', bloodCoefficient: 0.5 };
        } else if (member1.gender === 'male' && member2.gender === 'female') {
            return { relationship: '兄妹', bloodCoefficient: 0.5 };
        } else if (member1.gender === 'female' && member2.gender === 'male') {
            return { relationship: '姐弟', bloodCoefficient: 0.5 };
        } else {
            return { relationship: '姐妹', bloodCoefficient: 0.5 };
        }
    }
    
    // 检查叔侄/姑侄关系
    const member1Uncles = [];
    member1.parents.forEach(parentId => {
        const parent = familyData.members.find(m => m.id === parentId);
        if (parent && parent.parents) {
            parent.parents.forEach(grandparentId => {
                const grandparent = familyData.members.find(m => m.id === grandparentId);
                if (grandparent && grandparent.children) {
                    grandparent.children.forEach(uncleId => {
                        if (uncleId !== parentId) {
                            const uncle = familyData.members.find(m => m.id === uncleId);
                            if (uncle) {
                                member1Uncles.push(uncle);
                            }
                        }
                    });
                }
            });
        }
    });
    
    const uncleNephewRelation = member1Uncles.find(uncle => uncle.id === memberId2);
    if (uncleNephewRelation) {
        const relation = uncleNephewRelation.gender === 'male' ? 
            (member1.gender === 'male' ? '叔叔' : '姑姑') : 
            (member1.gender === 'male' ? '侄子' : '侄女');
        return { relationship: relation, bloodCoefficient: 0.25 };
    }
    
    // 检查父系祖先关系
    if (member1.patrilineal && member1.patrilineal.ancestors.includes(memberId2)) {
        const ancestorIndex = member1.patrilineal.ancestors.indexOf(memberId2);
        let relation = '';
        switch(ancestorIndex) {
            case 0: relation = '父亲'; break;
            case 1: relation = '祖父'; break;
            case 2: relation = '曾祖父'; break;
            case 3: relation = '高祖父'; break;
            default: relation = '祖先';
        }
        return { relationship: relation, bloodCoefficient: 1 / Math.pow(2, ancestorIndex + 2) };
    }
    
    // 检查父系后代关系
    if (member1.patrilineal && member1.patrilineal.descendants.includes(memberId2)) {
        const descendantMember = familyData.members.find(m => m.id === memberId2);
        if (descendantMember && descendantMember.patrilineal) {
            const generationDiff = descendantMember.patrilineal.depth - member1.patrilineal.depth;
            let relation = '';
            switch(generationDiff) {
                case 1: relation = member1.gender === 'male' ? '儿子' : '女儿'; break;
                case 2: relation = member1.gender === 'male' ? '孙子' : '孙女'; break;
                case 3: relation = member1.gender === 'male' ? '曾孙子' : '曾孙女'; break;
                case 4: relation = member1.gender === 'male' ? '高孙子' : '高孙女'; break;
                default: relation = '后代';
            }
            return { relationship: relation, bloodCoefficient: 1 / Math.pow(2, generationDiff + 1) };
        }
    }
    
    // 默认返回未知关系
    return { relationship: '未知', bloodCoefficient: 0 };
}

// 更新父母关系
function updateParentRelation(memberId, oldParents, newParents) {
    // 移除旧的父母关系
    oldParents.forEach(parentId => {
        // 如果旧父母不在新父母列表中，移除关系
        if (!newParents.includes(parentId)) {
            // 从成员的父母列表中移除
            const member = familyData.members.find(m => m.id === memberId);
            if (member) {
                member.parents = member.parents.filter(id => id !== parentId);
            }
            
            // 从父母的子女列表中移除
            const parent = familyData.members.find(m => m.id === parentId);
            if (parent && parent.children) {
                parent.children = parent.children.filter(id => id !== memberId);
            }
        }
    });
    
    // 添加新的父母关系
    newParents.forEach(parentId => {
        // 如果新父母不在旧父母列表中，添加关系
        if (!oldParents.includes(parentId)) {
            // 确保成员的父母列表存在
            const member = familyData.members.find(m => m.id === memberId);
            if (member && !member.parents.includes(parentId)) {
                member.parents.push(parentId);
            }
            
            // 确保父母的子女列表存在并添加成员
            const parent = familyData.members.find(m => m.id === parentId);
            if (parent) {
                if (!parent.children) {
                    parent.children = [];
                }
                if (!parent.children.includes(memberId)) {
                    parent.children.push(memberId);
                }
            }
        }
    });
}

// 更新配偶关系
function updateSpouseRelation(memberId, oldSpouse, newSpouse) {
    // 移除旧的配偶关系
    if (oldSpouse && oldSpouse !== newSpouse) {
        const oldSpouseMember = familyData.members.find(m => m.id === oldSpouse);
        if (oldSpouseMember) {
            oldSpouseMember.spouse = null;
        }
    }
    
    // 添加新的配偶关系
    if (newSpouse && newSpouse !== oldSpouse) {
        const newSpouseMember = familyData.members.find(m => m.id === newSpouse);
        if (newSpouseMember) {
            newSpouseMember.spouse = memberId;
        }
    }
}

// 删除成员
function deleteMember(memberId) {
    if (!checkAdminPermission()) return;
    
    if (!confirm('确定要删除这个成员吗？此操作不可撤销。')) return;
    
    // 找到要删除的成员
    const memberToDelete = familyData.members.find(m => m.id === memberId);
    if (!memberToDelete) return;
    
    // 1. 清理父母关系 - 从父母的children列表中移除该成员
    memberToDelete.parents.forEach(parentId => {
        const parent = familyData.members.find(m => m.id === parentId);
        if (parent && parent.children) {
            parent.children = parent.children.filter(id => id !== memberId);
        }
    });
    
    // 2. 清理配偶关系 - 移除配偶的spouse引用
    if (memberToDelete.spouse) {
        const spouse = familyData.members.find(m => m.id === memberToDelete.spouse);
        if (spouse) {
            spouse.spouse = null;
        }
    }
    
    // 3. 清理子女关系 - 从所有子女的parents列表中移除该成员
    familyData.members.forEach(member => {
        if (member.parents && member.parents.includes(memberId)) {
            member.parents = member.parents.filter(id => id !== memberId);
        }
    });
    
    // 4. 从members数组中移除该成员
    familyData.members = familyData.members.filter(m => m.id !== memberId);
    
    // 保存数据
    saveDataToDatabase();
    
    // 刷新页面内容
    const currentPage = window.location.pathname.split('/').pop();
    if (currentPage === 'members.html') {
        renderMembersGrid();
    }
}

// Excel导入功能
function showExcelImportModal() {
    const modal = document.getElementById('excelImportModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeExcelImportModal() {
    const modal = document.getElementById('excelImportModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    // 重置文件选择
    document.getElementById('excelFileInput').value = '';
    document.getElementById('selectedFileName').textContent = '未选择文件';
}

function handleExcelFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('selectedFileName').textContent = file.name;
    } else {
        document.getElementById('selectedFileName').textContent = '未选择文件';
    }
}

function downloadTemplate() {
    // 创建模板数据
    const templateData = [
        ['姓名', '性别', '出生日期', '世代', '手机号', '微信号', '邮箱', '现居地', '个人简介', '父亲姓名', '母亲姓名', '配偶姓名'],
        ['张三', 'male', '1980-01-01', '2', '13800138000', 'zhangsan', 'zhangsan@example.com', '北京', '家族第二代成员', '张父', '张母', '李四'],
        ['李四', 'female', '1982-02-02', '2', '13900139000', 'lisi', 'lisi@example.com', '上海', '张三的配偶', '', '', '张三']
    ];
    
    // 创建工作簿和工作表
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(templateData);
    XLSX.utils.book_append_sheet(wb, ws, '家族成员模板');
    
    // 下载文件
    XLSX.writeFile(wb, '家族成员导入模板.xlsx');
}

// 导出Excel数据
function exportExcelData() {
    // 准备导出数据
    const excelData = [
        ['ID', '姓名', '性别', '出生日期', '去世日期', '联系方式', '职业', '教育背景', '住址', '备注', 
         '父母ID', '父亲姓名', '母亲姓名', '配偶ID', '配偶姓名', 
         '子女ID', '子女姓名', '兄弟姐妹ID', '兄弟姐妹姓名']
    ];
    
    // 遍历所有成员
    familyData.members.forEach(member => {
        // 获取父亲和母亲信息
        const father = member.parents && member.parents.length > 0 ? familyData.members.find(m => m.id === member.parents[0]) : null;
        const mother = member.parents && member.parents.length > 1 ? familyData.members.find(m => m.id === member.parents[1]) : null;
        
        // 获取配偶信息
        const spouse = member.spouse ? familyData.members.find(m => m.id === member.spouse) : null;
        
        // 获取子女信息
        const children = member.children ? familyData.members.filter(m => m.parents && m.parents.includes(member.id)) : [];
        
        // 获取兄弟姐妹信息
        const siblings = member.parents ? 
            familyData.members.filter(m => 
                m.id !== member.id && 
                m.parents && 
                m.parents.length > 0 && 
                (m.parents.includes(member.parents[0]) || 
                (member.parents.length > 1 && m.parents.includes(member.parents[1])))
            ) : [];
        
        // 添加成员数据行
        excelData.push([
            member.id,
            member.name,
            member.gender,
            member.birthDate || '',
            member.deathDate || '',
            member.contact || '',
            member.occupation || '',
            member.education || '',
            member.address || '',
            member.notes || '',
            member.parents ? member.parents.join(',') : '',
            father ? father.name : '',
            mother ? mother.name : '',
            member.spouse || '',
            spouse ? spouse.name : '',
            member.children ? member.children.join(',') : '',
            children.map(c => c.name).join(','),
            siblings.map(s => s.id).join(','),
            siblings.map(s => s.name).join(',')
        ]);
    });
    
    // 创建Excel工作簿和工作表
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(excelData);
    
    // 设置列宽
    ws['!cols'] = [
        { wch: 10 }, // ID
        { wch: 15 }, // 姓名
        { wch: 8 }, // 性别
        { wch: 15 }, // 出生日期
        { wch: 15 }, // 去世日期
        { wch: 20 }, // 联系方式
        { wch: 20 }, // 职业
        { wch: 20 }, // 教育背景
        { wch: 30 }, // 住址
        { wch: 30 }, // 备注
        { wch: 20 }, // 父母ID
        { wch: 15 }, // 父亲姓名
        { wch: 15 }, // 母亲姓名
        { wch: 10 }, // 配偶ID
        { wch: 15 }, // 配偶姓名
        { wch: 20 }, // 子女ID
        { wch: 30 }, // 子女姓名
        { wch: 20 }, // 兄弟姐妹ID
        { wch: 30 } // 兄弟姐妹姓名
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, '家族成员');
    
    // 下载文件
    XLSX.writeFile(wb, `家族成员数据-${new Date().toISOString().slice(0, 10)}.xlsx`);
}

// 导出JSON数据
function exportJSON() {
    const dataToExport = {
        familyData: familyData,
        exportDate: new Date().toISOString(),
        version: '1.0'
    };
    
    const jsonString = JSON.stringify(dataToExport, null, 2);
    const blob = new Blob([jsonString], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `family-tree-data-${new Date().toISOString().slice(0, 10)}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function importExcelData() {
    const fileInput = document.getElementById('excelFileInput');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('请先选择要导入的Excel文件！');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const sheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[sheetName];
            
            // 将Excel数据转换为JSON格式
            const jsonData = XLSX.utils.sheet_to_json(worksheet, {
                header: ['姓名', '性别', '出生日期', '世代', '手机号', '微信号', '邮箱', '现居地', '个人简介', '父亲姓名', '母亲姓名', '配偶姓名'],
                defval: ''
            });
            
            // 过滤掉标题行
            const membersData = jsonData.slice(1).filter(member => member.姓名.trim() !== '');
            
            // 日期格式转换函数
            function parseExcelDate(dateValue) {
                if (!dateValue) return '';
                
                // 如果是数字，可能是Excel的日期序列号
                if (typeof dateValue === 'number') {
                    // Excel日期序列号是从1900年1月1日开始的天数
                    // 注意：Excel错误地将1900年视为闰年，所以需要特殊处理
                    const excelEpoch = new Date(1899, 11, 31);
                    const days = Math.round(dateValue);
                    const date = new Date(excelEpoch.getTime() + days * 24 * 60 * 60 * 1000);
                    
                    // 转换为YYYY-MM-DD格式
                    return date.toISOString().split('T')[0];
                }
                
                // 如果是字符串，尝试解析
                if (typeof dateValue === 'string') {
                    // 去除两端空格
                    dateValue = dateValue.trim();
                    if (!dateValue) return '';
                    
                    // 尝试直接解析
                    const date = new Date(dateValue);
                    if (!isNaN(date.getTime())) {
                        return date.toISOString().split('T')[0];
                    }
                    
                    // 尝试解析常见的中文日期格式
                    const datePatterns = [
                        /^(\d{4})[年/-](\d{1,2})[月/-](\d{1,2})[日]?$/,
                        /^(\d{1,2})[月/-](\d{1,2})[日/-](\d{4})$/,
                        /^(\d{4})(\d{2})(\d{2})$/,
                        /^(\d{2})(\d{2})(\d{4})$/
                    ];
                    
                    for (const pattern of datePatterns) {
                        const match = dateValue.match(pattern);
                        if (match) {
                            let year, month, day;
                            
                            if (pattern === datePatterns[0]) {
                                // YYYY-MM-DD 或 YYYY/MM/DD 或 YYYY年MM月DD日
                                year = match[1];
                                month = match[2].padStart(2, '0');
                                day = match[3].padStart(2, '0');
                            } else if (pattern === datePatterns[1]) {
                                // MM-DD-YYYY 或 MM/DD/YYYY 或 MM月DD日YYYY年
                                month = match[1].padStart(2, '0');
                                day = match[2].padStart(2, '0');
                                year = match[3];
                            } else if (pattern === datePatterns[2]) {
                                // YYYYMMDD
                                year = match[1];
                                month = match[2];
                                day = match[3];
                            } else {
                                // MMDDYYYY
                                month = match[1];
                                day = match[2];
                                year = match[3];
                            }
                            
                            const dateStr = `${year}-${month}-${day}`;
                            const date = new Date(dateStr);
                            if (!isNaN(date.getTime())) {
                                return dateStr;
                            }
                        }
                    }
                }
                
                // 如果无法解析，返回原始值
                return dateValue;
            }
            
            // 先导入所有成员数据（不处理关系）
            let importCount = 0;
            const importedMembers = [];
            
            membersData.forEach(member => {
                const memberData = {
                    name: member.姓名,
                    gender: member.性别 === '男' ? 'male' : member.性别 === '女' ? 'female' : member.性别,
                    birthDate: parseExcelDate(member.出生日期),
                    generation: parseInt(member.世代) || 1,
                    phone: member.手机号,
                    wechat: member.微信号,
                    email: member.邮箱,
                    location: member.现居地,
                    description: member.个人简介,
                    parents: [],
                    spouse: null,
                    children: []
                };
                
                const savedMember = saveMember(memberData);
                importedMembers.push({
                    ...savedMember,
                    父亲姓名: member.父亲姓名,
                    母亲姓名: member.母亲姓名,
                    配偶姓名: member.配偶姓名
                });
                importCount++;
            });
            
            // 处理家族关系（第二次遍历）
            importedMembers.forEach(member => {
                const parents = [];
                let spouseId = null;
                
                // 查找父亲
                if (member.父亲姓名) {
                    const father = familyData.members.find(m => 
                        m.name === member.父亲姓名 && m.gender === 'male'
                    );
                    if (father) {
                        parents.push(father.id);
                    }
                }
                
                // 查找母亲
                if (member.母亲姓名) {
                    const mother = familyData.members.find(m => 
                        m.name === member.母亲姓名 && m.gender === 'female'
                    );
                    if (mother) {
                        parents.push(mother.id);
                    }
                }
                
                // 查找配偶
                if (member.配偶姓名) {
                    const spouse = familyData.members.find(m => 
                        m.name === member.配偶姓名 && m.id !== member.id
                    );
                    if (spouse) {
                        spouseId = spouse.id;
                    }
                }
                
                // 更新成员关系
                if (parents.length > 0 || spouseId !== null) {
                    const memberIndex = familyData.members.findIndex(m => m.id === member.id);
                    if (memberIndex !== -1) {
                        familyData.members[memberIndex].parents = parents;
                        familyData.members[memberIndex].spouse = spouseId;
                        
                        // 处理双向关系
                        if (parents.length > 0) {
                            updateParentChildRelations(parents, member.id);
                        }
                        if (spouseId !== null) {
                            updateSpouseRelation(spouseId, member.id);
                        }
                    }
                }
            });
            
            // 保存数据
            saveDataToStorage();
            
            alert(`成功导入 ${importCount} 个家族成员！`);
            closeExcelImportModal();
            renderMembersGrid(); // 重新渲染成员列表
        } catch (error) {
            console.error('导入失败:', error);
            alert('Excel文件格式错误，请检查文件是否符合模板要求！');
        }
    };
    
    reader.readAsArrayBuffer(file);
}

// 相册页面初始化
function initializeGalleryPage() {
    // 检查管理员权限
    if (!checkAdminPermission()) {
        return;
    }
    
    renderPhotosGrid();
    initializeGalleryFilters();
    initializePhotoUpload();
    updateAdminStatus();
    
    // 搜索功能
    const searchInput = document.getElementById('photoSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterPhotos);
    }
}

// 渲染照片网格
function renderPhotosGrid() {
    const container = document.getElementById('photosGrid');
    const emptyState = document.getElementById('emptyGalleryState');
    
    if (!container) return;
    
    if (familyData.photos.length === 0) {
        container.classList.add('hidden');
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    if (emptyState) emptyState.classList.add('hidden');
    
    container.innerHTML = familyData.photos.map(photo => `
        <div class="photo-card bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden"
             onclick="showPhotoViewer('${photo.id}')">
            <div class="aspect-square overflow-hidden">
                <img src="${photo.url}" alt="${photo.title}" class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-amber-900 mb-2 truncate">${photo.title}</h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">${photo.description}</p>
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>${photo.uploadDate}</span>
                    <span class="capitalize">${photo.category}</span>
                </div>
            </div>
        </div>
    `).join('');
}

// 初始化相册筛选器
function initializeGalleryFilters() {
    const yearFilter = document.getElementById('yearFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortBy = document.getElementById('sortBy');
    
    if (yearFilter) yearFilter.addEventListener('change', filterPhotos);
    if (categoryFilter) categoryFilter.addEventListener('change', filterPhotos);
    if (sortBy) sortBy.addEventListener('change', filterPhotos);
}

// 筛选照片
function filterPhotos() {
    const searchTerm = document.getElementById('photoSearchInput')?.value.toLowerCase() || '';
    const yearFilter = document.getElementById('yearFilter')?.value || '';
    const categoryFilter = document.getElementById('categoryFilter')?.value || '';
    const sortBy = document.getElementById('sortBy')?.value || 'newest';
    
    let filteredPhotos = familyData.photos.filter(photo => {
        const matchesSearch = photo.title.toLowerCase().includes(searchTerm) ||
                            photo.description.toLowerCase().includes(searchTerm) ||
                            photo.tags.some(tag => tag.toLowerCase().includes(searchTerm));
        const matchesYear = !yearFilter || photo.uploadDate.startsWith(yearFilter);
        const matchesCategory = !categoryFilter || photo.category === categoryFilter;
        
        return matchesSearch && matchesYear && matchesCategory;
    });
    
    // 排序
    filteredPhotos.sort((a, b) => {
        switch(sortBy) {
            case 'oldest':
                return new Date(a.uploadDate) - new Date(b.uploadDate);
            case 'name':
                return a.title.localeCompare(b.title);
            case 'newest':
            default:
                return new Date(b.uploadDate) - new Date(a.uploadDate);
        }
    });
    
    // 重新渲染
    const container = document.getElementById('photosGrid');
    if (!container) return;
    
    container.innerHTML = filteredPhotos.map(photo => `
        <div class="photo-card bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden"
             onclick="showPhotoViewer('${photo.id}')">
            <div class="aspect-square overflow-hidden">
                <img src="${photo.url}" alt="${photo.title}" class="w-full h-full object-cover">
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-amber-900 mb-2 truncate">${photo.title}</h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">${photo.description}</p>
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>${photo.uploadDate}</span>
                    <span class="capitalize">${photo.category}</span>
                </div>
            </div>
        </div>
    `).join('');
}

// 初始化照片上传
function initializePhotoUpload() {
    const uploadArea = document.getElementById('uploadArea');
    const photoInput = document.getElementById('photoInput');
    const selectPhotosBtn = document.getElementById('selectPhotosBtn');
    const firstUploadBtn = document.getElementById('firstUploadBtn');
    
    if (uploadArea) {
        uploadArea.addEventListener('click', () => photoInput?.click());
        uploadArea.addEventListener('dragover', handleDragOver);
        uploadArea.addEventListener('drop', handlePhotoDrop);
    }
    
    if (photoInput) {
        photoInput.addEventListener('change', handlePhotoSelect);
    }
    
    if (selectPhotosBtn) {
        selectPhotosBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // 阻止事件冒泡，避免触发uploadArea的click事件
            photoInput?.click();
        });
    }
    
    if (firstUploadBtn) {
        firstUploadBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // 阻止事件冒泡，避免触发uploadArea的click事件
            photoInput?.click();
        });
    }
}

// 处理拖拽上传
function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('dragover');
}

function handlePhotoDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
    
    const files = Array.from(e.dataTransfer.files);
    handlePhotoFiles(files);
}

function handlePhotoSelect(e) {
    const files = Array.from(e.target.files);
    handlePhotoFiles(files);
}

function handlePhotoFiles(files) {
    const imageFiles = files.filter(file => file.type.startsWith('image/'));
    
    if (imageFiles.length === 0) {
        alert('请选择图片文件');
        return;
    }
    
    // 处理第一张图片，其他图片暂时跳过（可以扩展为批量处理）
    const file = imageFiles[0];
    const reader = new FileReader();
    reader.onload = function(e) {
        // 创建临时照片对象
        const tempPhoto = {
            id: 'photo_' + Date.now() + Math.random().toString(36).substr(2, 9),
            url: e.target.result,
            title: file.name.replace(/\.[^/.]+$/, ""),
            description: '新上传的照片',
            uploader: 'member_1', // 假设当前用户
            uploadDate: new Date().toISOString().split('T')[0],
            category: 'family', // 默认分类
            tags: [],
            relatedMembers: []
        };
        
        // 显示编辑模态框让用户设置信息
        showPhotoEditModal(tempPhoto, true);
    };
    reader.readAsDataURL(file);
}

// 显示照片编辑模态框
function showPhotoEditModal(photo, isNew = false) {
    const modal = document.getElementById('photoEditModal');
    if (!modal) return;
    
    currentEditingPhoto = photo;
    
    // 填充表单
    document.getElementById('editPhotoTitle').value = photo.title;
    document.getElementById('editPhotoDescription').value = photo.description;
    document.getElementById('editPhotoCategory').value = photo.category;
    
    // 显示模态框
    modal.classList.remove('hidden');
    
    // 绑定表单提交事件
    const form = document.getElementById('photoEditForm');
    if (form) {
        form.onsubmit = function(e) {
            e.preventDefault();
            
            // 更新照片信息
            photo.title = document.getElementById('editPhotoTitle').value;
            photo.description = document.getElementById('editPhotoDescription').value;
            photo.category = document.getElementById('editPhotoCategory').value;
            
            if (isNew) {
                // 保存新照片
                familyData.photos.push(photo);
            } else {
                // 更新现有照片
                const index = familyData.photos.findIndex(p => p.id === photo.id);
                if (index !== -1) {
                    familyData.photos[index] = photo;
                }
            }
            
            // 保存数据
            saveDataToDatabase();
            renderPhotosGrid();
            
            // 关闭模态框
            modal.classList.add('hidden');
        };
    }
    
    // 绑定关闭按钮
    const closeBtn = document.getElementById('closeEditModalBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    }
}

// 显示照片查看器
function showPhotoViewer(photoId) {
    const photo = familyData.photos.find(p => p.id === photoId);
    if (!photo) return;
    
    const modal = document.getElementById('photoViewerModal');
    if (!modal) return;
    
    // 更新照片信息
    document.getElementById('currentPhotoTitle').textContent = photo.title;
    document.getElementById('currentPhotoDescription').textContent = photo.description;
    document.getElementById('currentPhotoDate').textContent = photo.uploadDate;
    document.getElementById('currentPhotoCategory').textContent = photo.category;
    
    const uploader = familyData.members.find(m => m.id === photo.uploader);
    document.getElementById('currentPhotoUploader').textContent = uploader ? uploader.name : '未知';
    
    // 初始化轮播
    const carouselList = document.getElementById('photoCarouselList');
    if (carouselList) {
        carouselList.innerHTML = familyData.photos.map(p => `
            <li class="splide__slide">
                <img src="${p.url}" alt="${p.title}">
            </li>
        `).join('');
        
        // 初始化Splide轮播
        if (photoCarousel) {
            photoCarousel.destroy();
        }
        
        photoCarousel = new Splide('#photoCarousel', {
            type: 'loop',
            perPage: 1,
            start: familyData.photos.findIndex(p => p.id === photoId)
        }).mount();
    }
    
    modal.classList.remove('hidden');
    
    // 为编辑按钮绑定事件
    const editBtn = document.getElementById('editBlogBtn');
    if (editBtn) {
        editBtn.onclick = function() {
            showBlogEditor(blog.id);
            modal.classList.add('hidden');
        };
    }
    
    // 为删除按钮绑定事件
    const deleteBtn = document.getElementById('deleteBlogBtn');
    if (deleteBtn) {
        deleteBtn.onclick = function() {
            deleteBlog(blog.id);
        };
    }
}

// 博客页面初始化
function initializeBlogsPage() {
    // 检查管理员权限
    if (!checkAdminPermission()) {
        return;
    }
    
    renderBlogsGrid();
    initializeBlogFilters();
    initializeBlogEditor();
    updateAdminStatus();
    
    // 写博客按钮
    const writeBlogBtn = document.getElementById('writeBlogBtn');
    const firstBlogBtn = document.getElementById('firstBlogBtn');
    
    if (writeBlogBtn) {
        writeBlogBtn.addEventListener('click', () => showBlogEditor());
    }
    
    if (firstBlogBtn) {
        firstBlogBtn.addEventListener('click', () => showBlogEditor());
    }
    
    // 搜索功能
    const searchInput = document.getElementById('blogSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterBlogs);
    }
}

// 渲染博客网格
function renderBlogsGrid() {
    const container = document.getElementById('blogsGrid');
    const emptyState = document.getElementById('emptyBlogsState');
    
    if (!container) return;
    
    if (familyData.blogs.length === 0) {
        container.classList.add('hidden');
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    if (emptyState) emptyState.classList.add('hidden');
    
    container.innerHTML = familyData.blogs.map(blog => {
        const author = familyData.members.find(m => m.id === blog.author);
        const readTime = Math.ceil(blog.content.length / 500); // 假设每分钟读500字
        
        return `
            <div class="blog-card bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden cursor-pointer"
                 onclick="showBlogDetail('${blog.id}')">
                ${blog.photos && blog.photos.length > 0 ? `
                    <div class="aspect-video overflow-hidden">
                        <img src="${familyData.photos.find(p => p.id === blog.photos[0])?.url || ''}" 
                             alt="${blog.title}" class="w-full h-full object-cover">
                    </div>
                ` : ''}
                
                <div class="p-6">
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs font-medium rounded-full capitalize">
                            ${getCategoryName(blog.category)}
                        </span>
                        <span class="text-xs text-gray-500">${readTime}分钟阅读</span>
                    </div>
                    
                    <h3 class="hero-title text-xl font-semibold text-amber-900 mb-2 line-clamp-2">
                        ${blog.title}
                    </h3>
                    
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        ${blog.summary || blog.content.replace(/<[^>]*>/g, '').substring(0, 100) + '...'}
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-amber-200 flex items-center justify-center">
                                <span class="text-amber-800 font-semibold text-sm">
                                    ${author ? author.name.charAt(0) : '未'}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    ${author ? author.name : '未知作者'}
                                </p>
                                <p class="text-xs text-gray-500">${blog.publishDate}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3 text-sm text-gray-500">
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                <span>${blog.likes || 0}</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>${blog.comments ? blog.comments.length : 0}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// 获取分类名称
function getCategoryName(category) {
    const categoryNames = {
        'family': '家族历史',
        'memoir': '回忆录',
        'event': '家族活动',
        'tradition': '传统文化',
        'news': '家族新闻'
    };
    return categoryNames[category] || category;
}

// 初始化博客筛选器
function initializeBlogFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    const authorFilter = document.getElementById('authorFilter');
    
    // 填充作者选项
    if (authorFilter) {
        const authors = [...new Set(familyData.blogs.map(blog => blog.author))];
        authorFilter.innerHTML = '<option value="">所有作者</option>' + 
            authors.map(authorId => {
                const author = familyData.members.find(m => m.id === authorId);
                return `<option value="${authorId}">${author ? author.name : '未知作者'}</option>`;
            }).join('');
        
        authorFilter.addEventListener('change', filterBlogs);
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterBlogs);
    }
}

// 筛选博客
function filterBlogs() {
    const searchTerm = document.getElementById('blogSearchInput')?.value.toLowerCase() || '';
    const categoryFilter = document.getElementById('categoryFilter')?.value || '';
    const authorFilter = document.getElementById('authorFilter')?.value || '';
    
    const filteredBlogs = familyData.blogs.filter(blog => {
        const matchesSearch = blog.title.toLowerCase().includes(searchTerm) ||
                            blog.summary.toLowerCase().includes(searchTerm) ||
                            blog.tags.some(tag => tag.toLowerCase().includes(searchTerm));
        const matchesCategory = !categoryFilter || blog.category === categoryFilter;
        const matchesAuthor = !authorFilter || blog.author === authorFilter;
        
        return matchesSearch && matchesCategory && matchesAuthor;
    });
    
    // 重新渲染筛选后的博客
    const container = document.getElementById('blogsGrid');
    if (!container) return;
    
    container.innerHTML = filteredBlogs.map(blog => {
        const author = familyData.members.find(m => m.id === blog.author);
        const readTime = Math.ceil(blog.content.length / 500);
        
        return `
            <div class="blog-card bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden cursor-pointer"
                     onclick="showBlogDetail('${blog.id}')">
                    ${blog.photos && blog.photos.length > 0 ? `
                        <div class="aspect-video overflow-hidden">
                            <img src="${familyData.photos.find(p => p.id === blog.photos[0])?.url || ''}" 
                                 alt="${blog.title}" class="w-full h-full object-cover">
                        </div>
                    ` : ''}
                    
                    <div class="p-6">
                        <div class="flex items-center space-x-2 mb-3">
                            <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs font-medium rounded-full capitalize">
                                ${getCategoryName(blog.category)}
                            </span>
                            <span class="text-xs text-gray-500">${readTime}分钟阅读</span>
                        </div>
                        
                        <h3 class="hero-title text-xl font-semibold text-amber-900 mb-2 line-clamp-2">
                            ${blog.title}
                        </h3>
                        
                        <p class="text-gray-600 mb-4 line-clamp-3">
                            ${blog.summary || blog.content.replace(/<[^>]*>/g, '').substring(0, 100) + '...'}
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-full bg-amber-200 flex items-center justify-center">
                                    <span class="text-amber-800 font-semibold text-sm">
                                        ${author ? author.name.charAt(0) : '未'}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        ${author ? author.name : '未知作者'}
                                    </p>
                                    <p class="text-xs text-gray-500">${blog.publishDate}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3 text-sm text-gray-500">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span>${blog.likes || 0}</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <span>${blog.comments ? blog.comments.length : 0}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
    }).join('');
}

// 显示博客编辑器
function showBlogEditor(blogId = null) {
    const modal = document.getElementById('blogEditorModal');
    if (!modal) return;
    
    currentEditingBlog = blogId ? familyData.blogs.find(b => b.id === blogId) : null;
    
    const title = document.getElementById('editorTitle');
    const blogTitle = document.getElementById('blogTitle');
    const blogCategory = document.getElementById('blogCategory');
    const blogTags = document.getElementById('blogTags');
    const blogSummary = document.getElementById('blogSummary');
    const blogContentEditor = document.getElementById('blogContentEditor');
    
    if (currentEditingBlog) {
        // 编辑模式
        title.textContent = '编辑博客';
        blogTitle.value = currentEditingBlog.title;
        blogCategory.value = currentEditingBlog.category;
        blogTags.value = currentEditingBlog.tags ? currentEditingBlog.tags.join(', ') : '';
        blogSummary.value = currentEditingBlog.summary || '';
        blogContentEditor.innerHTML = currentEditingBlog.content;
    } else {
        // 新建模式
        title.textContent = '写博客';
        document.getElementById('blogEditorForm').reset();
        blogContentEditor.innerHTML = '';
    }
    
    modal.classList.remove('hidden');
    
    // 为编辑按钮绑定事件
    const editBtn = document.getElementById('editBlogBtn');
    if (editBtn) {
        editBtn.onclick = function() {
            showBlogEditor(blog.id);
            modal.classList.add('hidden');
        };
    }
    
    // 为删除按钮绑定事件
    const deleteBtn = document.getElementById('deleteBlogBtn');
    if (deleteBtn) {
        deleteBtn.onclick = function() {
            deleteBlog(blog.id);
        };
    }
}

// 显示博客详情
function showBlogDetail(blogId) {
    const blog = familyData.blogs.find(b => b.id === blogId);
    if (!blog) return;
    
    const modal = document.getElementById('blogDetailModal');
    const titleEl = document.getElementById('detailBlogTitle');
    const contentEl = document.getElementById('blogDetailContent');
    
    if (!modal || !titleEl || !contentEl) return;
    
    titleEl.textContent = blog.title;
    
    const author = familyData.members.find(m => m.id === blog.author);
    const readTime = Math.ceil(blog.content.length / 500);
    
    contentEl.innerHTML = `
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-12 h-12 rounded-full bg-amber-200 flex items-center justify-center">
                <span class="text-amber-800 font-bold text-lg">
                    ${author ? author.name.charAt(0) : '未'}
                </span>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900">${author ? author.name : '未知作者'}</h4>
                <p class="text-sm text-gray-500">${blog.publishDate} · ${readTime}分钟阅读</p>
            </div>
        </div>
        
        ${blog.summary ? `
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6">
                <p class="text-amber-800 font-medium">${blog.summary}</p>
            </div>
        ` : ''}
        
        <div class="blog-content prose max-w-none">
            ${blog.content}
        </div>
        
        ${blog.tags && blog.tags.length > 0 ? `
            <div class="mt-6">
                <div class="flex flex-wrap gap-2">
                    ${blog.tags.map(tag => `
                        <span class="tag px-3 py-1 bg-amber-100 text-amber-800 text-sm font-medium rounded-full">
                            #${tag}
                        </span>
                    `).join('')}
                </div>
            </div>
        ` : ''}
        
        <div class="flex items-center justify-between mt-8 pt-6 border-t">
            <div class="flex items-center space-x-4">
                <button class="flex items-center space-x-2 text-gray-600 hover:text-amber-600 transition-colors"
                        onclick="likeBlog('${blog.id}')">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <span>${blog.likes || 0}</span>
                </button>
                
                <button class="flex items-center space-x-2 text-gray-600 hover:text-amber-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>${blog.comments ? blog.comments.length : 0}</span>
                </button>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');

    // 为编辑按钮绑定事件
    const editBtn = document.getElementById('editBlogBtn');
    if (editBtn) {
        editBtn.onclick = function() {
            showBlogEditor(blog.id);
            modal.classList.add('hidden');
        };
    }
    
    // 为删除按钮绑定事件
    const deleteBtn = document.getElementById('deleteBlogBtn');
    if (deleteBtn) {
        deleteBtn.onclick = function() {
            deleteBlog(blog.id);
        };
    }
}

// 点赞博客
function likeBlog(blogId) {
    const blog = familyData.blogs.find(b => b.id === blogId);
    if (blog) {
        blog.likes = (blog.likes || 0) + 1;
        saveDataToDatabase();
        
        // 重新显示博客详情以更新点赞数
        showBlogDetail(blogId);
    }
}

// 初始化博客编辑器
function initializeBlogEditor() {
    const form = document.getElementById('blogEditorForm');
    const saveBtn = document.getElementById('saveBlogBtn');
    const draftBtn = document.getElementById('saveDraftBtn');
    const closeBtn = document.getElementById('closeEditorBtn');
    const cancelBtn = document.getElementById('cancelEditorBtn');
    
    // 编辑器按钮
    const editorBtns = document.querySelectorAll('.editor-btn');
    editorBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const command = this.dataset.command;
            if (command) {
                document.execCommand(command, false, null);
            }
        });
    });
    
    // 插入图片按钮
    const insertImageBtn = document.getElementById('insertImageBtn');
    if (insertImageBtn) {
        insertImageBtn.addEventListener('click', function() {
            const imageUrl = prompt('请输入图片URL:');
            if (imageUrl) {
                document.execCommand('insertImage', false, imageUrl);
            }
        });
    }
    
    // 表单提交
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveBlog(false); // 发布
        });
    }
    // 移除重复的点击事件，避免创建两篇博客
    // if (saveBtn) {
    //     saveBtn.addEventListener('click', () => saveBlog(false));
    // }
    
    if (draftBtn) {
        draftBtn.addEventListener('click', () => saveBlog(true));
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            document.getElementById('blogEditorModal').classList.add('hidden');
        });
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            document.getElementById('blogEditorModal').classList.add('hidden');
        });
    }
}

// 保存博客
function saveBlog(isDraft = false) {
    const title = document.getElementById('blogTitle').value.trim();
    const category = document.getElementById('blogCategory').value;
    const tags = document.getElementById('blogTags').value.split(',').map(tag => tag.trim()).filter(tag => tag);
    const summary = document.getElementById('blogSummary').value.trim();
    const content = document.getElementById('blogContentEditor').innerHTML;
    
    if (!title) {
        alert('请输入博客标题');
        return;
    }
    
    if (!category) {
        alert('请选择博客分类');
        return;
    }
    
    if (!content || content === '<div><br></div>') {
        alert('请输入博客内容');
        return;
    }
    
    const blogData = {
        title,
        category,
        tags,
        summary,
        content,
        publishDate: new Date().toISOString().split('T')[0]
    };
    
    if (currentEditingBlog) {
        // 更新现有博客
        Object.assign(currentEditingBlog, blogData);
    } else {
        // 添加新博客
        const newBlog = {
            id: 'blog_' + Date.now(),
            author: 'member_1', // 假设当前用户
            likes: 0,
            comments: [],
            ...blogData
        };
        familyData.blogs.push(newBlog);
    }
    
    saveDataToStorage();
    
    // 关闭模态框
    document.getElementById('blogEditorModal').classList.add('hidden');
    
    // 刷新博客列表
    renderBlogsGrid();
    
    // 刷新最新动态
    updateLatestActivities();
}

// 删除博客
function deleteBlog(blogId) {
    if (!checkAdminPermission()) return;
    
    if (!confirm('确定要删除这篇博客吗？此操作不可撤销。')) return;
    
    familyData.blogs = familyData.blogs.filter(b => b.id !== blogId);
    saveDataToStorage();
    
    // 关闭详情模态框
    document.getElementById('blogDetailModal').classList.add('hidden');
    
    // 刷新博客列表
    renderBlogsGrid();
}

// 管理员权限管理函数
function updateAdminStatus() {
    // 更新导航栏显示管理员状态
    const navContainer = document.querySelector('nav .max-w-7xl .flex');
    if (!navContainer) return;
    
    // 检查是否已存在管理员状态显示
    const existingAdminStatus = document.getElementById('adminStatus');
    if (existingAdminStatus) {
        existingAdminStatus.remove();
    }
    
    // 创建管理员状态显示
    const adminStatus = document.createElement('div');
    adminStatus.id = 'adminStatus';
    adminStatus.className = 'flex items-center space-x-2';
    
    if (isAdmin) {
        adminStatus.innerHTML = `
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <span class="text-sm text-green-600 font-medium">管理员</span>
                <button onclick="logoutAdmin()" class="text-sm text-gray-600 hover:text-red-600 transition-colors">退出</button>
            </div>
        `;
    } else {
        adminStatus.innerHTML = `
            <div class="flex items-center space-x-2">
                <a href="admin.html" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors">
                    管理员登录
                </a>
            </div>
        `;
    }
    
    navContainer.appendChild(adminStatus);
}

function checkAdminPermission() {
    if (!isAdmin) {
        alert('您需要管理员权限才能执行此操作！');
        return false;
    }
    return true;
}

function logoutAdmin() {
    if (confirm('确定要退出管理员登录吗？')) {
        localStorage.removeItem('adminLoggedIn');
        localStorage.removeItem('adminUsername');
        isAdmin = false;
        updateAdminStatus();
        
        // 如果在非公开页面，跳转到首页
        const currentPage = window.location.pathname.split('/').pop();
        if (!['index.html', ''].includes(currentPage)) {
            window.location.href = 'index.html';
        }
    }
}

// 辅助函数
function expandFamilyTree() {
    // 展开家族树的所有节点
    const chart = echarts.getInstanceByDom(document.getElementById('familyTree'));
    if (chart) {
        chart.dispatchAction({
            type: 'treeExpandAndCollapse',
            seriesIndex: 0,
            dataIndex: 0
        });
    }
}

// 世系大图初始化函数
function initializeGenealogyChart() {
    const chartDom = document.getElementById('genealogyChart');
    if (!chartDom) return;
    
    const chart = echarts.init(chartDom);
    
    // 将家族数据转换为世系图所需的格式
    const genealogyData = convertToGenealogyData();
    
    // 配置世系图的选项
    const option = {
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                const member = params.data.member;
                return `
                    <div class="font-medium">${member.name}</div>
                    <div class="text-sm text-gray-600">第${member.generation}代</div>
                    <div class="text-sm text-gray-600">${member.gender === 'male' ? '男' : '女'}</div>
                    ${member.birthDate ? `<div class="text-sm text-gray-600">出生日期：${member.birthDate}</div>` : ''}
                `;
            }
        },
        animationDurationUpdate: 750,
        series: [
            {
                type: 'tree',
                data: [genealogyData],
                top: '5%',
                left: '7%',
                bottom: '5%',
                right: '7%',
                symbolSize: 25,
                label: {
                    position: 'top',
                    verticalAlign: 'middle',
                    align: 'right',
                    fontSize: 12,
                    formatter: '{b}'
                },
                leaves: {
                    label: {
                        position: 'bottom',
                        verticalAlign: 'middle',
                        align: 'left'
                    }
                },
                emphasis: {
                    focus: 'descendant'
                },
                expandAndCollapse: true,
                animationDuration: 550,
                animationDurationUpdate: 750,
                layout: 'radial',
                orient: 'LR',
                initialTreeDepth: -1
            }
        ]
    };
    
    chart.setOption(option);
    
    // 添加点击事件
    chart.on('click', function(params) {
        if (params.data.member) {
            showMemberDetail(params.data.member);
        }
    });
    
    // 窗口大小变化时调整图表
    window.addEventListener('resize', function() {
        chart.resize();
    });
}

// 世系图数据转换函数
function convertToGenealogyData() {
    if (familyData.members.length === 0) {
        return { name: '暂无数据', value: 1 };
    }
    
    // 找到最早的一代（祖先）
    const earliestGeneration = Math.min(...familyData.members.map(member => member.generation));
    const ancestors = familyData.members.filter(member => member.generation === earliestGeneration);
    
    // 构建世系树
    if (ancestors.length > 0) {
        return buildGenealogyTree(ancestors[0]);
    } else {
        return { name: '暂无数据', value: 1 };
    }
}

// 构建世系树
function buildGenealogyTree(member) {
    const treeNode = {
        name: member.name,
        value: 1,
        member: member,
        children: []
    };
    
    // 查找所有子女
    const children = familyData.members.filter(m => 
        (m.fatherId === member.id || m.motherId === member.id) && 
        // 只添加直接子女，避免重复
        (!m.fatherId || m.fatherId === member.id) && 
        (!m.motherId || m.motherId === member.id)
    );
    
    // 递归构建子树
    children.forEach(child => {
        treeNode.children.push(buildGenealogyTree(child));
    });
    
    // 如果没有子女，确保children数组存在
    if (treeNode.children.length === 0) {
        treeNode.children = [];
    }
    
    return treeNode;
}

// 添加世系图切换事件
function initializeGenealogyChartEvents() {
    const familyTreeBtn = document.getElementById('showFamilyTreeBtn');
    const genealogyChartBtn = document.getElementById('showGenealogyChartBtn');
    const familyTreeDiv = document.getElementById('familyTree');
    const genealogyChartDiv = document.getElementById('genealogyChart');
    
    if (familyTreeBtn && genealogyChartBtn && familyTreeDiv && genealogyChartDiv) {
        familyTreeBtn.addEventListener('click', function() {
            familyTreeDiv.classList.remove('hidden');
            genealogyChartDiv.classList.add('hidden');
            familyTreeBtn.classList.add('bg-amber-600', 'text-white');
            familyTreeBtn.classList.remove('bg-white', 'text-amber-800', 'border', 'border-amber-300');
            genealogyChartBtn.classList.remove('bg-amber-600', 'text-white');
            genealogyChartBtn.classList.add('bg-white', 'text-amber-800', 'border', 'border-amber-300');
        });
        
        genealogyChartBtn.addEventListener('click', function() {
            familyTreeDiv.classList.add('hidden');
            genealogyChartDiv.classList.remove('hidden');
            familyTreeBtn.classList.remove('bg-amber-600', 'text-white');
            familyTreeBtn.classList.add('bg-white', 'text-amber-800', 'border', 'border-amber-300');
            genealogyChartBtn.classList.add('bg-amber-600', 'text-white');
            genealogyChartBtn.classList.remove('bg-white', 'text-amber-800', 'border', 'border-amber-300');
            
            // 初始化世系图
            initializeGenealogyChart();
        });
    }
}

// 更新初始化事件监听器，添加世系图事件
function updateEventListenersForGenealogy() {
    const currentPage = window.location.pathname.split('/').pop();
    if (currentPage === 'index.html' || currentPage === '') {
        initializeGenealogyChartEvents();
        
        // 初始化世系图（隐藏状态）
        initializeGenealogyChart();
    }
}

// 添加全局函数
window.showMemberDetail = showMemberDetail;
window.editMember = editMember;
window.deleteMember = deleteMember;
window.showPhotoViewer = showPhotoViewer;
window.showBlogDetail = showBlogDetail;
window.likeBlog = likeBlog;
window.showBlogEditor = showBlogEditor;
window.deleteBlog = deleteBlog;
window.updateLatestActivities = updateLatestActivities;
window.initializeGenealogyChart = initializeGenealogyChart;
// 调试功能：在控制台显示家族数据
window.debugFamilyData = function() {
    console.log('Family Data:', familyData);
    return familyData;
}

// 配偶信息页面初始化
function initializeSpousesPage() {
    // 检查管理员权限
    if (!checkAdminPermission()) {
        return;
    }
    
    renderSpousesList();
    initializeSpouseFilters();
    updateAdminStatus();
    
    // 搜索功能
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterSpouses);
    }
    
    // 导出配偶信息按钮
    const exportSpousesBtn = document.getElementById('exportSpousesBtn');
    if (exportSpousesBtn) {
        exportSpousesBtn.addEventListener('click', exportSpousesData);
    }
}

// 渲染配偶信息列表
function renderSpousesList() {
    const container = document.getElementById('spousesList');
    const emptyState = document.getElementById('emptyState');
    
    if (!container) return;
    
    // 获取所有有配偶的成员，并去重
    const spousePairs = new Set();
    familyData.members.forEach(member => {
        if (member.spouse && member.id < member.spouse) {
            spousePairs.add(`${member.id}-${member.spouse}`);
        }
    });
    
    const spousePairsArray = Array.from(spousePairs).map(pair => {
        const [id1, id2] = pair.split('-');
        const member1 = familyData.members.find(m => m.id === id1);
        const member2 = familyData.members.find(m => m.id === id2);
        return [member1, member2];
    });
    
    if (spousePairsArray.length === 0) {
        container.classList.add('hidden');
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    if (emptyState) emptyState.classList.add('hidden');
    
    container.innerHTML = spousePairsArray.map(([member1, member2]) => {
        const generation = Math.max(member1.generation || 1, member2.generation || 1);
        return `
            <div class="spouse-card bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
                <!-- 标题栏 -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="hero-title text-xl font-semibold text-amber-900 flex items-center gap-2">
                        <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm">第${generation}代</span>
                        <span>${member1.name} & ${member2.name}</span>
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 text-sm">
                            ${member1.gender === 'male' ? '夫妻' : '伉俪'}
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- 成员1信息 -->
                    <div class="flex-1 bg-gradient-to-br from-amber-50 to-white rounded-xl p-4 shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer" 
                         onclick="showMemberDetail(familyData.members.find(m => m.id === '${member1.id}'))">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-200 to-amber-300 flex items-center justify-center overflow-hidden shadow-sm">
                                ${member1.avatar ? 
                                    `<img src="${member1.avatar}" alt="${member1.name}" class="w-full h-full object-cover">` :
                                    `<span class="text-amber-800 font-bold text-xl">${member1.name.charAt(0)}</span>`
                                }
                            </div>
                            <div>
                                <h4 class="hero-title text-lg font-semibold text-amber-900">${member1.name}</h4>
                                <p class="text-gray-600 text-sm">${member1.gender === 'male' ? '男' : '女'}</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            ${member1.birthDate ? `<p class="flex items-center gap-2"><span class="text-gray-500">出生:</span> <span class="text-gray-700">${member1.birthDate}</span></p>` : ''}
                            ${member1.deathDate ? `<p class="flex items-center gap-2"><span class="text-gray-500">去世:</span> <span class="text-gray-700">${member1.deathDate}</span></p>` : ''}
                            ${member1.location ? `<p class="flex items-center gap-2"><span class="text-gray-500">现居:</span> <span class="text-gray-700">${member1.location}</span></p>` : ''}
                            ${member1.occupation ? `<p class="flex items-center gap-2"><span class="text-gray-500">职业:</span> <span class="text-gray-700">${member1.occupation}</span></p>` : ''}
                        </div>
                    </div>
                    
                    <!-- 婚姻关系符号 -->
                    <div class="flex items-center justify-center">
                        <div class="text-4xl text-amber-600 animate-pulse-slow">💑</div>
                    </div>
                    
                    <!-- 成员2信息 -->
                    <div class="flex-1 bg-gradient-to-br from-amber-50 to-white rounded-xl p-4 shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer" 
                         onclick="showMemberDetail(familyData.members.find(m => m.id === '${member2.id}'))">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-200 to-amber-300 flex items-center justify-center overflow-hidden shadow-sm">
                                ${member2.avatar ? 
                                    `<img src="${member2.avatar}" alt="${member2.name}" class="w-full h-full object-cover">` :
                                    `<span class="text-amber-800 font-bold text-xl">${member2.name.charAt(0)}</span>`
                                }
                            </div>
                            <div>
                                <h4 class="hero-title text-lg font-semibold text-amber-900">${member2.name}</h4>
                                <p class="text-gray-600 text-sm">${member2.gender === 'male' ? '男' : '女'}</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            ${member2.birthDate ? `<p class="flex items-center gap-2"><span class="text-gray-500">出生:</span> <span class="text-gray-700">${member2.birthDate}</span></p>` : ''}
                            ${member2.deathDate ? `<p class="flex items-center gap-2"><span class="text-gray-500">去世:</span> <span class="text-gray-700">${member2.deathDate}</span></p>` : ''}
                            ${member2.location ? `<p class="flex items-center gap-2"><span class="text-gray-500">现居:</span> <span class="text-gray-700">${member2.location}</span></p>` : ''}
                            ${member2.occupation ? `<p class="flex items-center gap-2"><span class="text-gray-500">职业:</span> <span class="text-gray-700">${member2.occupation}</span></p>` : ''}
                        </div>
                    </div>
                </div>
                
                <!-- 共同子女信息 -->
                ${(() => {
                    const children1 = member1.children || [];
                    const children2 = member2.children || [];
                    const commonChildren = children1.filter(childId => children2.includes(childId));
                    
                    if (commonChildren.length > 0) {
                        return `
                            <div class="mt-6 border-t border-amber-100 pt-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="hero-title text-lg font-semibold text-amber-900">共同子女</h4>
                                    <span class="text-amber-600 font-medium">${commonChildren.length}人</span>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    ${commonChildren.map(childId => {
                                        const child = familyData.members.find(m => m.id === childId);
                                        return child ? `
                                            <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm cursor-pointer hover:bg-amber-200 hover:shadow-md transition-all duration-200 transform hover:scale-105" 
                                                  onclick="showMemberDetail(familyData.members.find(m => m.id === '${child.id}'))">
                                                ${child.name}
                                                <span class="text-xs text-gray-500 ml-1">
                                                    ${child.gender === 'male' ? '子' : '女'}
                                                </span>
                                            </span>
                                        ` : '';
                                    }).join('')}
                                </div>
                            </div>
                        `;
                    }
                    return '';
                })()}
                
                <!-- 操作按钮 -->
                <div class="flex flex-wrap justify-center md:justify-end gap-3 mt-6 pt-4 border-t border-amber-100">
                    <button class="flex-1 md:flex-initial bg-amber-600 hover:bg-amber-700 text-white py-2 px-4 rounded-lg font-medium transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1" 
                            onclick="showMemberDetail(familyData.members.find(m => m.id === '${member1.id}'))">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            查看${member1.gender === 'male' ? '丈夫' : '妻子'}详情
                        </span>
                    </button>
                    <button class="flex-1 md:flex-initial bg-amber-600 hover:bg-amber-700 text-white py-2 px-4 rounded-lg font-medium transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1" 
                            onclick="showMemberDetail(familyData.members.find(m => m.id === '${member2.id}'))">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            查看${member2.gender === 'male' ? '丈夫' : '妻子'}详情
                        </span>
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// 初始化配偶筛选器
function initializeSpouseFilters() {
    const generationFilter = document.getElementById('generationFilter');
    if (generationFilter) {
        generationFilter.addEventListener('change', filterSpouses);
    }
}

// 筛选配偶信息
function filterSpouses() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const generationFilter = document.getElementById('generationFilter').value;
    
    // 获取所有有配偶的成员，并去重
    const spousePairs = new Set();
    familyData.members.forEach(member => {
        if (member.spouse && member.id < member.spouse) {
            spousePairs.add(`${member.id}-${member.spouse}`);
        }
    });
    
    const filteredPairs = Array.from(spousePairs).filter(pair => {
        const [id1, id2] = pair.split('-');
        const member1 = familyData.members.find(m => m.id === id1);
        const member2 = familyData.members.find(m => m.id === id2);
        
        // 搜索条件
        const matchesSearch = !searchTerm || 
            member1.name.toLowerCase().includes(searchTerm) || 
            member2.name.toLowerCase().includes(searchTerm);
        
        // 世代筛选
        const matchesGeneration = !generationFilter || 
            (member1.generation && member1.generation.toString() === generationFilter) || 
            (member2.generation && member2.generation.toString() === generationFilter);
        
        return matchesSearch && matchesGeneration;
    });
    
    const container = document.getElementById('spousesList');
    const emptyState = document.getElementById('emptyState');
    
    if (!container) return;
    
    if (filteredPairs.length === 0) {
        container.classList.add('hidden');
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    if (emptyState) emptyState.classList.add('hidden');
    
    // 渲染筛选后的结果
    container.innerHTML = filteredPairs.map(pair => {
        const [id1, id2] = pair.split('-');
        const member1 = familyData.members.find(m => m.id === id1);
        const member2 = familyData.members.find(m => m.id === id2);
        const generation = Math.max(member1.generation || 1, member2.generation || 1);
        return `
            <div class="spouse-card bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
                <!-- 标题栏 -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="hero-title text-xl font-semibold text-amber-900 flex items-center gap-2">
                        <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm">第${generation}代</span>
                        <span>${member1.name} & ${member2.name}</span>
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 text-sm">
                            ${member1.gender === 'male' ? '夫妻' : '伉俪'}
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- 成员1信息 -->
                    <div class="flex-1 bg-gradient-to-br from-amber-50 to-white rounded-xl p-4 shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer" 
                         onclick="showMemberDetail(familyData.members.find(m => m.id === '${member1.id}'))">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-200 to-amber-300 flex items-center justify-center overflow-hidden shadow-sm">
                                ${member1.avatar ? 
                                    `<img src="${member1.avatar}" alt="${member1.name}" class="w-full h-full object-cover">` :
                                    `<span class="text-amber-800 font-bold text-xl">${member1.name.charAt(0)}</span>`
                                }
                            </div>
                            <div>
                                <h4 class="hero-title text-lg font-semibold text-amber-900">${member1.name}</h4>
                                <p class="text-gray-600 text-sm">${member1.gender === 'male' ? '男' : '女'}</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            ${member1.birthDate ? `<p class="flex items-center gap-2"><span class="text-gray-500">出生:</span> <span class="text-gray-700">${member1.birthDate}</span></p>` : ''}
                            ${member1.deathDate ? `<p class="flex items-center gap-2"><span class="text-gray-500">去世:</span> <span class="text-gray-700">${member1.deathDate}</span></p>` : ''}
                            ${member1.location ? `<p class="flex items-center gap-2"><span class="text-gray-500">现居:</span> <span class="text-gray-700">${member1.location}</span></p>` : ''}
                            ${member1.occupation ? `<p class="flex items-center gap-2"><span class="text-gray-500">职业:</span> <span class="text-gray-700">${member1.occupation}</span></p>` : ''}
                        </div>
                    </div>
                    
                    <!-- 婚姻关系符号 -->
                    <div class="flex items-center justify-center">
                        <div class="text-4xl text-amber-600 animate-pulse-slow">💑</div>
                    </div>
                    
                    <!-- 成员2信息 -->
                    <div class="flex-1 bg-gradient-to-br from-amber-50 to-white rounded-xl p-4 shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer" 
                         onclick="showMemberDetail(familyData.members.find(m => m.id === '${member2.id}'))">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-200 to-amber-300 flex items-center justify-center overflow-hidden shadow-sm">
                                ${member2.avatar ? 
                                    `<img src="${member2.avatar}" alt="${member2.name}" class="w-full h-full object-cover">` :
                                    `<span class="text-amber-800 font-bold text-xl">${member2.name.charAt(0)}</span>`
                                }
                            </div>
                            <div>
                                <h4 class="hero-title text-lg font-semibold text-amber-900">${member2.name}</h4>
                                <p class="text-gray-600 text-sm">${member2.gender === 'male' ? '男' : '女'}</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            ${member2.birthDate ? `<p class="flex items-center gap-2"><span class="text-gray-500">出生:</span> <span class="text-gray-700">${member2.birthDate}</span></p>` : ''}
                            ${member2.deathDate ? `<p class="flex items-center gap-2"><span class="text-gray-500">去世:</span> <span class="text-gray-700">${member2.deathDate}</span></p>` : ''}
                            ${member2.location ? `<p class="flex items-center gap-2"><span class="text-gray-500">现居:</span> <span class="text-gray-700">${member2.location}</span></p>` : ''}
                            ${member2.occupation ? `<p class="flex items-center gap-2"><span class="text-gray-500">职业:</span> <span class="text-gray-700">${member2.occupation}</span></p>` : ''}
                        </div>
                    </div>
                </div>
                
                <!-- 共同子女信息 -->
                ${(() => {
                    const children1 = member1.children || [];
                    const children2 = member2.children || [];
                    const commonChildren = children1.filter(childId => children2.includes(childId));
                    
                    if (commonChildren.length > 0) {
                        return `
                            <div class="mt-6 border-t border-amber-100 pt-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="hero-title text-lg font-semibold text-amber-900">共同子女</h4>
                                    <span class="text-amber-600 font-medium">${commonChildren.length}人</span>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    ${commonChildren.map(childId => {
                                        const child = familyData.members.find(m => m.id === childId);
                                        return child ? `
                                            <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm cursor-pointer hover:bg-amber-200 hover:shadow-md transition-all duration-200 transform hover:scale-105" 
                                                  onclick="showMemberDetail(familyData.members.find(m => m.id === '${child.id}'))">
                                                ${child.name}
                                                <span class="text-xs text-gray-500 ml-1">
                                                    ${child.gender === 'male' ? '子' : '女'}
                                                </span>
                                            </span>
                                        ` : '';
                                    }).join('')}
                                </div>
                            </div>
                        `;
                    }
                    return '';
                })()}
                
                <!-- 操作按钮 -->
                <div class="flex flex-wrap justify-center md:justify-end gap-3 mt-6 pt-4 border-t border-amber-100">
                    <button class="flex-1 md:flex-initial bg-amber-600 hover:bg-amber-700 text-white py-2 px-4 rounded-lg font-medium transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1" 
                            onclick="showMemberDetail(familyData.members.find(m => m.id === '${member1.id}'))">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            查看${member1.gender === 'male' ? '丈夫' : '妻子'}详情
                        </span>
                    </button>
                    <button class="flex-1 md:flex-initial bg-amber-600 hover:bg-amber-700 text-white py-2 px-4 rounded-lg font-medium transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1" 
                            onclick="showMemberDetail(familyData.members.find(m => m.id === '${member2.id}'))">
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            查看${member2.gender === 'male' ? '丈夫' : '妻子'}详情
                        </span>
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// 导出配偶信息
function exportSpousesData() {
    // 获取所有有配偶的成员，并去重
    const spousePairs = new Set();
    familyData.members.forEach(member => {
        if (member.spouse && member.id < member.spouse) {
            spousePairs.add(`${member.id}-${member.spouse}`);
        }
    });
    
    const spousesData = Array.from(spousePairs).map(pair => {
        const [id1, id2] = pair.split('-');
        const member1 = familyData.members.find(m => m.id === id1);
        const member2 = familyData.members.find(m => m.id === id2);
        
        // 获取共同子女
        const children1 = member1.children || [];
        const children2 = member2.children || [];
        const commonChildren = children1.filter(childId => children2.includes(childId));
        
        return {
            member1: {
                name: member1.name,
                gender: member1.gender,
                generation: member1.generation,
                birthDate: member1.birthDate,
                location: member1.location
            },
            member2: {
                name: member2.name,
                gender: member2.gender,
                generation: member2.generation,
                birthDate: member2.birthDate,
                location: member2.location
            },
            commonChildren: commonChildren.map(childId => {
                const child = familyData.members.find(m => m.id === childId);
                return child ? child.name : '';
            }).filter(Boolean)
        };
    });
    
    const dataStr = JSON.stringify(spousesData, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'family_spouses_data.json';
    link.click();
    URL.revokeObjectURL(url);
}

// 添加全局函数
window.initializeSpousesPage = initializeSpousesPage;