using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using FamilyTreeSystem.Data;
using FamilyTreeSystem.Models;
using System.Collections.Generic;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Http;
using System.IO;
using System.Linq;
using System;

namespace FamilyTreeSystem.Controllers
{
    public class FamilyMemberController : Controller
    {
        private readonly FamilyTreeContext _context;

        public FamilyMemberController(FamilyTreeContext context)
        {
            _context = context;
        }

        // GET: FamilyMember
        public async Task<IActionResult> Index(string? searchText, bool? showAll)
        {
            // 从Session获取showAll的持久化状态
            string? sessionShowAll = HttpContext.Session.GetString("ShowAll");
            bool isShowAll = sessionShowAll == "true";
            
            // 如果请求中提供了showAll参数，使用该参数值并更新Session
            if (showAll.HasValue)
            {
                HttpContext.Session.SetString("ShowAll", showAll.Value.ToString());
                ViewBag.ShowAll = showAll.Value;
            }
            else
            {
                // 否则使用Session中保存的值
                ViewBag.ShowAll = isShowAll;
            }
            
            // 构建查询
            var familyMembers = _context.FamilyMembers
                .Include(m => m.Spouse)
                .Include(m => m.Ziwei)
                .Include(m => m.Clan)
                .Include(m => m.Father)
                .Include(m => m.Mother)
                .AsQueryable();
            
            // 如果ViewBag.ShowAll为false，只显示族内人员（有宗族关系的）
            if (!ViewBag.ShowAll)
            {
                familyMembers = familyMembers.Where(m => m.ClanId != null);
            }
            
            // 添加多字段搜索条件
            if (!string.IsNullOrEmpty(searchText))
            {
                // 尝试将搜索文本转换为数字，用于匹配世代字段
                bool isNumber = int.TryParse(searchText, out int generationNumber);
                
                familyMembers = familyMembers.Where(m => 
                    // 匹配姓名（使用全名）
                    m.FullName.Contains(searchText) || 
                    // 匹配世代（如果搜索文本是数字）
                    (isNumber && m.Generation == generationNumber) || 
                    // 匹配字辈
                    (m.Ziwei != null && m.Ziwei.ZiweiChar.Contains(searchText))
                );
            }
            
            return View(await familyMembers.ToListAsync());
        }

        // GET: FamilyMember/Details/5
        public async Task<IActionResult> Details(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var familyMember = await _context.FamilyMembers
                .Include(m => m.Spouse)
                .Include(m => m.Ziwei)
                .Include(m => m.Clan)
                .Include(m => m.Father)
                .Include(m => m.Mother)
                .FirstOrDefaultAsync(m => m.Id == id);
            if (familyMember == null)
            {
                return NotFound();
            }

            // 重新设置所有必要的ViewBag数据
            ViewBag.ZiweiList = _context.Ziwei.AsNoTracking().ToList();
            ViewBag.ClanList = _context.Clans.AsNoTracking().ToList();
            ViewBag.SpouseList = _context.FamilyMembers.AsNoTracking().ToList();
            ViewBag.FatherList = _context.FamilyMembers.AsNoTracking().Where(m => m.Gender == "Male").ToList();
            ViewBag.MotherList = _context.FamilyMembers.AsNoTracking().Where(m => m.Gender == "Female").ToList();
            return View(familyMember);
        }

        // GET: FamilyMember/Create
        public IActionResult Create()
        {
            ViewBag.ZiweiList = _context.Ziwei.ToList();
            ViewBag.ClanList = _context.Clans.ToList();

            // 添加配偶列表，显示所有性别家族成员
            ViewBag.SpouseList = _context.FamilyMembers.ToList();
            
            // 添加父亲和母亲列表
            ViewBag.FatherList = _context.FamilyMembers.Where(m => m.Gender == "Male").ToList();
            ViewBag.MotherList = _context.FamilyMembers.Where(m => m.Gender == "Female").ToList();
            return View();
        }

        // POST: FamilyMember/Create
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Create(string? FullName, string? Gender, bool IsAlive, int? BirthOrder, string? Description, int? SpouseId, int? ZiweiId, int? ClanId, string? ImageUrl, int? Generation, FamilyTreeSystem.Models.Enums.RelationshipType? SpouseRelationType, string? FormerName, string? PhoneNumber, string? CertificateAddress, string? IDCardNumber, string? EducationLevel, string? GraduateSchool, string? WorkUnit, decimal? MeritContribution, int? FatherId, int? MotherId, IFormFile? profileImage, string? spouseType, string? newSpouseName, string? newSpouseGender, int? newSpouseGeneration)
        {
            // 创建FamilyMember实例，用于返回视图
            var familyMember = new FamilyMember
            {
                FullName = FullName ?? "",
                Gender = Gender ?? "",
                IsAlive = IsAlive,
                BirthOrder = BirthOrder,
                Description = Description,
                SpouseId = SpouseId,
                ZiweiId = ZiweiId,
                ClanId = ClanId,
                ImageUrl = ImageUrl,
                Generation = Generation,
                SpouseRelationType = SpouseRelationType,
                FormerName = FormerName,
                PhoneNumber = PhoneNumber,
                CertificateAddress = CertificateAddress,
                IDCardNumber = IDCardNumber,
                EducationLevel = EducationLevel,
                GraduateSchool = GraduateSchool,
                WorkUnit = WorkUnit,
                MeritContribution = MeritContribution,
                FatherId = FatherId,
                MotherId = MotherId,
                UserId = 1, // 设置默认用户ID
                CreatedAt = DateTime.Now,
                UpdatedAt = DateTime.Now
            };
            
            // 验证必填字段
            if (string.IsNullOrEmpty(FullName))
            {
                ModelState.AddModelError("FullName", "姓名不能为空");
            }
            
            if (string.IsNullOrEmpty(Gender))
            {
                ModelState.AddModelError("Gender", "性别不能为空");
            }
            
            if (ModelState.IsValid)
            {
                // 确保性别有默认值
                if (string.IsNullOrEmpty(Gender))
                {
                    familyMember.Gender = "Male";
                }
                
                // 处理文件上传
                if (profileImage != null && profileImage.Length > 0)
                {
                    string uploadPath = Path.Combine(Directory.GetCurrentDirectory(), "wwwroot", "images");
                    if (!Directory.Exists(uploadPath))
                    {
                        Directory.CreateDirectory(uploadPath);
                    }
                    
                    string fileName = Guid.NewGuid().ToString() + Path.GetExtension(profileImage.FileName);
                    string filePath = Path.Combine(uploadPath, fileName);
                    
                    using (var stream = new FileStream(filePath, FileMode.Create))
                    {
                        await profileImage.CopyToAsync(stream);
                    }
                    
                    familyMember.ImageUrl = $"/images/{fileName}";
                }
                
                _context.Add(familyMember);
                await _context.SaveChangesAsync();
                
                // 处理手动输入的配偶信息
                if (spouseType == "new" && !string.IsNullOrEmpty(newSpouseName))
                {
                    // 创建新配偶
                    var newSpouse = new FamilyMember
                    {
                        FullName = string.IsNullOrEmpty(newSpouseName) ? "未知" : newSpouseName, // 使用输入的名字作为全名
                        Gender = string.IsNullOrEmpty(newSpouseGender) ? "Male" : newSpouseGender,
                        Generation = newSpouseGeneration ?? familyMember.Generation, // 设置配偶世代为当前成员世代
                        UserId = 1, // 设置默认用户ID
                        CreatedAt = DateTime.Now,
                        UpdatedAt = DateTime.Now,
                        SpouseId = familyMember.Id // 设置新配偶的配偶ID为当前成员ID（双向关联）
                    };
                    
                    _context.Add(newSpouse);
                    await _context.SaveChangesAsync();
                    
                    // 直接使用已跟踪的实体更新配偶ID，避免创建新的实体实例
                    familyMember.SpouseId = newSpouse.Id;
                    await _context.SaveChangesAsync();
                }
                return RedirectToAction(nameof(Index));
            }
            // 重新设置所有必要的ViewBag数据
            ViewBag.ZiweiList = _context.Ziwei.ToList();
            ViewBag.ClanList = _context.Clans.ToList();
            ViewBag.SpouseList = _context.FamilyMembers.ToList();
            ViewBag.FatherList = _context.FamilyMembers.Where(m => m.Gender == "Male").ToList();
            ViewBag.MotherList = _context.FamilyMembers.Where(m => m.Gender == "Female").ToList();
            return View(familyMember);
        }

        // GET: FamilyMember/Edit/5
        public async Task<IActionResult> Edit(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var familyMember = await _context.FamilyMembers
                .Include(m => m.Spouse)
                .Include(m => m.Ziwei)
                .Include(m => m.Clan)
                .Include(m => m.Father)
                .Include(m => m.Mother)
                .AsNoTracking() // 使用AsNoTracking避免跟踪冲突
                .FirstOrDefaultAsync(m => m.Id == id);
            if (familyMember == null)
            {
                return NotFound();
            }
            
            // 添加配偶列表，显示所有性别家族成员
            ViewBag.SpouseList = _context.FamilyMembers.ToList();
            ViewBag.ZiweiList = _context.Ziwei.ToList();
            ViewBag.ClanList = _context.Clans.ToList();
            
            // 添加父亲和母亲列表
            ViewBag.FatherList = _context.FamilyMembers.Where(m => m.Gender == "Male").ToList();
            ViewBag.MotherList = _context.FamilyMembers.Where(m => m.Gender == "Female").ToList();
            
            return View(familyMember);
        }

        // POST: FamilyMember/Edit/5
        [HttpPost]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> Edit(int id, string? FullName, string? Gender, bool IsAlive, int? BirthOrder, string? Description, int? SpouseId, int? ZiweiId, int? ClanId, string? ImageUrl, int? Generation, FamilyTreeSystem.Models.Enums.RelationshipType? SpouseRelationType, string? FormerName, string? PhoneNumber, string? CertificateAddress, string? IDCardNumber, string? EducationLevel, string? GraduateSchool, string? WorkUnit, decimal? MeritContribution, int? FatherId, int? MotherId, IFormFile? profileImage, string? spouseType, string? newSpouseName, string? newSpouseGender, int? newSpouseGeneration)
        {
            // 先获取现有成员数据，用于验证和设置默认值
            var existingMember = await _context.FamilyMembers.AsNoTracking().FirstOrDefaultAsync(m => m.Id == id);
            if (existingMember == null)
            {
                return NotFound();
            }
            
            // 验证必填字段
            if (string.IsNullOrEmpty(FullName))
            {
                ModelState.AddModelError("FullName", "姓名不能为空");
            }
            
            if (string.IsNullOrEmpty(Gender))
            {
                ModelState.AddModelError("Gender", "性别不能为空");
            }

            if (ModelState.IsValid)
            {
                // 创建FamilyMember实例，确保所有required字段都被正确初始化
                var familyMember = new FamilyMember
                {
                    Id = id,
                    FullName = FullName ?? existingMember.FullName,
                    Gender = Gender ?? existingMember.Gender,
                    IsAlive = IsAlive,
                    BirthOrder = BirthOrder,
                    Description = Description,
                    SpouseId = SpouseId,
                    ZiweiId = ZiweiId,
                    ClanId = ClanId,
                    ImageUrl = ImageUrl,
                    Generation = Generation,
                    SpouseRelationType = SpouseRelationType,
                    FormerName = FormerName,
                    PhoneNumber = PhoneNumber,
                    CertificateAddress = CertificateAddress,
                    IDCardNumber = IDCardNumber,
                    EducationLevel = EducationLevel,
                    GraduateSchool = GraduateSchool,
                    WorkUnit = WorkUnit,
                    MeritContribution = MeritContribution,
                    FatherId = FatherId,
                    MotherId = MotherId,
                    UserId = 1, // 设置默认用户ID
                    CreatedAt = existingMember.CreatedAt, // 使用现有创建时间
                    UpdatedAt = DateTime.Now // 设置更新时间
                };
                
                try
                {
                    // 处理文件上传
                    if (profileImage != null && profileImage.Length > 0)
                    {
                        string uploadPath = Path.Combine(Directory.GetCurrentDirectory(), "wwwroot", "images");
                        if (!Directory.Exists(uploadPath))
                        {
                            Directory.CreateDirectory(uploadPath);
                        }
                        
                        string fileName = Guid.NewGuid().ToString() + Path.GetExtension(profileImage.FileName);
                        string filePath = Path.Combine(uploadPath, fileName);
                        
                        using (var stream = new FileStream(filePath, FileMode.Create))
                        {
                            await profileImage.CopyToAsync(stream);
                        }
                        
                        // 删除旧图片（如果存在）
                        if (!string.IsNullOrEmpty(existingMember.ImageUrl))
                        {
                            string oldFilePath = Path.Combine(Directory.GetCurrentDirectory(), "wwwroot", existingMember.ImageUrl.TrimStart('/'));
                            if (System.IO.File.Exists(oldFilePath))
                            {
                                System.IO.File.Delete(oldFilePath);
                            }
                        }
                        
                        familyMember.ImageUrl = $"/images/{fileName}";
                    }
                    else
                    {
                        // 如果没有上传新图片，使用现有图片URL
                        familyMember.ImageUrl = existingMember.ImageUrl;
                    }
                    
                    // 获取当前成员的原始配偶ID信息
                    var currentSpouseId = existingMember.SpouseId;
                    
                    if (currentSpouseId.HasValue)
                    {
                        // 获取旧配偶，使用AsNoTracking避免跟踪冲突
                        var oldSpouse = await _context.FamilyMembers.AsNoTracking().FirstOrDefaultAsync(m => m.Id == currentSpouseId.Value);
                        if (oldSpouse != null)
                        {
                            // 先检查是否有被跟踪的旧配偶实体，如果有则分离
                            var trackedOldSpouse = _context.ChangeTracker.Entries<FamilyMember>()
                                .FirstOrDefault(e => e.Entity.Id == oldSpouse.Id);
                            if (trackedOldSpouse != null)
                            {
                                trackedOldSpouse.State = EntityState.Detached;
                            }
                            // 解除旧配偶的配偶关系
                            oldSpouse.SpouseId = null;
                            _context.Update(oldSpouse);
                            await _context.SaveChangesAsync(); // 立即保存更改，释放实体跟踪
                        }
                    }
                    
                    // 处理配偶信息
                    if (spouseType == "new" && !string.IsNullOrEmpty(newSpouseName))
                    {
                        // 创建新配偶
                        var newSpouse = new FamilyMember
                        {
                            FullName = newSpouseName, // 使用输入的名字作为全名
                            Gender = string.IsNullOrEmpty(newSpouseGender) ? "Male" : newSpouseGender,
                            Generation = newSpouseGeneration ?? familyMember.Generation, // 设置配偶世代为当前成员世代
                            UserId = 1, // 设置默认用户ID
                            CreatedAt = DateTime.Now,
                            UpdatedAt = DateTime.Now,
                            SpouseId = familyMember.Id // 设置新配偶的配偶ID为当前成员ID（双向关联）
                        };
                        
                        _context.Add(newSpouse);
                        await _context.SaveChangesAsync();
                        
                        // 设置当前成员的配偶ID为新创建的配偶ID
                        familyMember.SpouseId = newSpouse.Id;
                    }
                    else if (spouseType == "existing" && SpouseId.HasValue)
                    {
                        // 获取新选择的配偶，使用AsNoTracking避免跟踪冲突
                        var newSpouse = await _context.FamilyMembers.AsNoTracking().FirstOrDefaultAsync(m => m.Id == SpouseId.Value);
                        if (newSpouse != null)
                        {
                            // 先检查是否有被跟踪的配偶实体，如果有则分离
                            var trackedSpouse = _context.ChangeTracker.Entries<FamilyMember>()
                                .FirstOrDefault(e => e.Entity.Id == newSpouse.Id);
                            if (trackedSpouse != null)
                            {
                                trackedSpouse.State = EntityState.Detached;
                            }
                            // 设置新配偶的配偶ID为当前成员ID（双向关联）
                            newSpouse.SpouseId = familyMember.Id;
                            _context.Update(newSpouse);
                            await _context.SaveChangesAsync(); // 立即保存更改，释放实体跟踪
                        }
                    }
                    else
                    {
                        // 如果没有选择配偶或手动输入，则清空配偶ID
                        familyMember.SpouseId = null;
                    }
                    
                    // 先检查是否有被跟踪的实体，如果有则分离
                    var trackedEntity = _context.ChangeTracker.Entries<FamilyMember>()
                        .FirstOrDefault(e => e.Entity.Id == familyMember.Id);
                    if (trackedEntity != null)
                    {
                        trackedEntity.State = EntityState.Detached;
                    }
                    
                    // 更新实体
                    _context.Update(familyMember);
                    await _context.SaveChangesAsync();
                }
                catch (DbUpdateConcurrencyException)
                {
                    if (!FamilyMemberExists(id))
                    {
                        return NotFound();
                    }
                    else
                    {
                        throw;
                    }
                }
                return RedirectToAction(nameof(Index));
            }
            
            // 创建用于返回视图的FamilyMember实例
            var modelToReturn = new FamilyMember
            {
                Id = id,
                FullName = FullName ?? existingMember.FullName,
                Gender = Gender ?? existingMember.Gender,
                IsAlive = IsAlive,
                BirthOrder = BirthOrder,
                Description = Description,
                SpouseId = SpouseId,
                ZiweiId = ZiweiId,
                ClanId = ClanId,
                ImageUrl = ImageUrl ?? existingMember.ImageUrl,
                Generation = Generation,
                SpouseRelationType = SpouseRelationType,
                FormerName = FormerName,
                PhoneNumber = PhoneNumber,
                CertificateAddress = CertificateAddress,
                IDCardNumber = IDCardNumber,
                EducationLevel = EducationLevel,
                GraduateSchool = GraduateSchool,
                WorkUnit = WorkUnit,
                MeritContribution = MeritContribution,
                FatherId = FatherId,
                MotherId = MotherId,
                UserId = existingMember.UserId,
                CreatedAt = existingMember.CreatedAt,
                UpdatedAt = DateTime.Now
            };
            
            // 重新填充所有必要的ViewBag数据
            ViewBag.SpouseList = _context.FamilyMembers.ToList();
            ViewBag.ZiweiList = _context.Ziwei.ToList();
            ViewBag.ClanList = _context.Clans.ToList();
            ViewBag.FatherList = _context.FamilyMembers.Where(m => m.Gender == "Male").ToList();
            ViewBag.MotherList = _context.FamilyMembers.Where(m => m.Gender == "Female").ToList();
            return View(modelToReturn);
        }

        // GET: FamilyMember/Delete/5
        public async Task<IActionResult> Delete(int? id)
        {
            if (id == null)
            {
                return NotFound();
            }

            var familyMember = await _context.FamilyMembers
                .FirstOrDefaultAsync(m => m.Id == id);
            if (familyMember == null)
            {
                return NotFound();
            }

            return View(familyMember);
        }

        // GET: FamilyMember/DeleteConfirmed
        public async Task<IActionResult> DeleteConfirmed(int? id)
        {
            // 如果有ID参数，返回特定的家族成员信息
            if (id.HasValue)
            {
                var familyMember = await _context.FamilyMembers.FindAsync(id.Value);
                if (familyMember != null)
                {
                    return View(familyMember);
                }
            }
            
            // 如果没有ID参数，返回空模型用于显示通用成功消息
            var emptyModel = new FamilyMember { FullName = "", Gender = "" };
            return View(emptyModel);
        }

        // POST: FamilyMember/Delete/5
        [HttpPost, ActionName("Delete")]
        [ValidateAntiForgeryToken]
        public async Task<IActionResult> DeleteConfirmed(int id)
        {
            try
            {
                // 使用AsNoTracking获取实体，避免跟踪冲突
                var familyMember = await _context.FamilyMembers.AsNoTracking().FirstOrDefaultAsync(m => m.Id == id);
                if (familyMember != null)
                {
                    // 使用事务来处理关联关系清理，确保数据一致性
                    using (var transaction = await _context.Database.BeginTransactionAsync())
                    {
                        try
                        {
                            // 不再需要清除父母引用，因为已经移除了FatherId和MotherId字段
                            
                            // 2. 清除所有与此人为配偶的引用（双向）
                            await _context.Database.ExecuteSqlRawAsync(
                                "UPDATE FamilyMembers SET SpouseId = NULL WHERE SpouseId = {0}", id);
                            
                            // 3. 删除关联的头像文件（如果存在）
                            if (!string.IsNullOrEmpty(familyMember.ImageUrl))
                            {
                                string filePath = Path.Combine(Directory.GetCurrentDirectory(), "wwwroot", familyMember.ImageUrl.TrimStart('/'));
                                if (System.IO.File.Exists(filePath))
                                {
                                    System.IO.File.Delete(filePath);
                                }
                            }
                            
                            // 4. 重新获取实体用于删除（确保没有跟踪冲突）
                            var memberToDelete = await _context.FamilyMembers.FindAsync(id);
                            if (memberToDelete != null)
                            {
                                _context.FamilyMembers.Remove(memberToDelete);
                                await _context.SaveChangesAsync();
                            }
                            
                            await transaction.CommitAsync();
                        }
                        catch
                        {
                            await transaction.RollbackAsync();
                            throw;
                        }
                    }
                }
                return RedirectToAction(nameof(Index));
            }
            catch (Exception ex)
            {
                // 记录错误日志（在实际应用中应该使用日志框架）
                Console.WriteLine($"删除家族成员时发生错误: {ex.Message}");
                
                // 返回错误信息给用户
                TempData["ErrorMessage"] = "删除成员时发生错误，请稍后重试。";
                return RedirectToAction(nameof(Index));
            }
        }

        private bool FamilyMemberExists(int id)
        {
            return _context.FamilyMembers.Any(e => e.Id == id);
        }
    }
}