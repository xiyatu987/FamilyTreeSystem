using System.ComponentModel;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace FamilyTreeSystem.Models
{
    /// <summary>
    /// 宗族模型
    /// </summary>
    public class Clan
    {
        /// <summary>
        /// 宗族主键ID
        /// </summary>
        [Key]
        [DisplayName("ID")]
        public int Id { get; set; }

        /// <summary>
        /// 族谱名称，必填，最长255个字符
        /// </summary>
        [Required]
        [StringLength(100)]
        [DisplayName("族谱名称")]
        public required string Name { get; set; }

        /// <summary>
        /// 姓氏，必填，最长20个字符
        /// </summary>
        [Required]
        [StringLength(20)]
        [DisplayName("姓氏")]
        public required string ClanSurname { get; set; }

        /// <summary>
        /// 堂号，最长50个字符
        /// </summary>
        [StringLength(50)]
        [DisplayName("堂号")]
        public string? HallName { get; set; }

        /// <summary>
        /// 始祖姓名，最长50个字符
        /// </summary>
        [StringLength(50)]
        [DisplayName("始祖姓名")]
        public string? AncestorName { get; set; }

        /// <summary>
        /// 始祖生辰，可空
        /// </summary>
        [DisplayName("始祖生辰")]
        public DateTime? AncestorBirthDate { get; set; }

        /// <summary>
        /// 原籍地址，最长200个字符
        /// </summary>
        [StringLength(200)]
        [DisplayName("原籍地址")]
        public string? OriginalAddress { get; set; }

        /// <summary>
        /// 地区，最长100个字符
        /// </summary>
        [StringLength(100)]
        [DisplayName("地区")]
        public string? Region { get; set; }

        /// <summary>
        /// 简介，可空
        /// </summary>
        [DisplayName("简介")]
        public string? Description { get; set; }

        /// <summary>
        /// 创建该宗族的用户ID，必填
        /// </summary>
        [Required]
        [DisplayName("用户ID")]
        public int UserId { get; set; }

        /// <summary>
        /// 宗族创建时间，默认当前时间
        /// </summary>
        [DisplayName("创建时间")]
        public DateTime CreatedAt { get; set; } = DateTime.Now;
        
        /// <summary>
        /// 宗族信息更新时间，默认当前时间
        /// </summary>
        [DisplayName("更新时间")]
        public DateTime UpdatedAt { get; set; } = DateTime.Now;

        /// <summary>
        /// 创建该宗族的用户导航属性
        /// </summary>
        [ForeignKey("UserId")]
        public User? User { get; set; }



        /// <summary>
        /// 该宗族包含的家族成员列表导航属性集合
        /// </summary>
        [DisplayName("家族成员列表")]
        public ICollection<FamilyMember> FamilyMembers { get; set; } = new List<FamilyMember>();
    }
}